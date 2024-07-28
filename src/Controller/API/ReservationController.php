<?php

namespace App\Controller\API;

use App\Entity\Reservation;
use App\Repository\ParkingSpaceRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ReservationController extends AbstractController
{

    private $em;

    private $userRepository;

    private $parkingSpaceRepository;

    private $jwtManager;

    private $tokenStorageInterface;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        ParkingSpaceRepository $parkingSpaceRepository,
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->parkingSpaceRepository = $parkingSpaceRepository;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/reservations-management/reservations', name: 'reservations.new', methods: ['POST'])]
    public function createReservation(
        Request $request,
        ReservationRepository $reservationRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $userEmailInToken = $decodedJwtToken['email'];

        $findUserConnectedWithToken = $this->userRepository->findOneByEmail($userEmailInToken);

        $reservationStartDate = $data['start_date'];
        $reservationEndDate = $data['end_date'];
        $parkingSpaceId = $data['parking_space_id'];
        $reservationStartTime = $data['start_time'];
        $reservationEndTime = $data['end_time'];

        if (strtotime($reservationEndDate) < strtotime($reservationStartDate)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Error in the end date, please check it.'
            ]);
        }

        $findParkingSpaceById = $this->parkingSpaceRepository->find($parkingSpaceId);

        if (!$findParkingSpaceById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NO_CONTENT,
                'message' => 'No parking found with that ID.'
            ]);
        }

        $reservationStartDateAndTime = $reservationStartDate . " " . $reservationStartTime;

        if ($reservationStartDateAndTime === null) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'The date and time of reservation beginning must not be null.'
            ]);
        }

        $reservationEndDateAndTime = $reservationEndDate . " " . $reservationEndTime;

        if ($reservationEndDateAndTime === null) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'The date and time of reservation end must not be null.'
            ]);
        }

        $reservationStartDateTimeInterface = DateTime::createFromFormat("Y-m-d H:i:s", $reservationStartDateAndTime);

        $reservationEndDateTimeInterface = DateTime::createFromFormat("Y-m-d H:i:s", $reservationEndDateAndTime);

        if($reservationEndDateTimeInterface < $reservationStartDateTimeInterface){
            return new JsonResponse([
                'status'=>JsonResponse::HTTP_BAD_REQUEST,
                'message'=>'There is an error in the date or time.'
            ]);
        }

        $isReservationAlreadyExist = $reservationRepository->findOneBy([
            'reservationDateTime'=>$reservationStartDateTimeInterface,
            'reservationEndDateTime'=>$reservationEndDateTimeInterface
        ]);

        if($isReservationAlreadyExist){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'There is already reservation related to this parking with that date and time.Please change your reservation.'
            ]);
        }

        $allReservationsRelatedToThatParkingSpaces = $reservationRepository->findBy(['parkingSpace'=>$findParkingSpaceById]);

        foreach($allReservationsRelatedToThatParkingSpaces as $allReservationsRelatedToThatParkingSpace){
            if(($reservationStartDateTimeInterface > $allReservationsRelatedToThatParkingSpace->getReservationDateTime() && $reservationEndDateTimeInterface <= $allReservationsRelatedToThatParkingSpace->getReservationEndDateTime())){
                return new JsonResponse([
                    'status'=>JsonResponse::HTTP_BAD_REQUEST,
                    'message'=> 'There is already a reservation between the date and time that you choose.'
                ]);
            }else if($reservationStartDateTimeInterface <= $allReservationsRelatedToThatParkingSpace->getReservationDateTime() && ($reservationEndDateTimeInterface > $allReservationsRelatedToThatParkingSpace->getReservationDateTime() && ($reservationEndDateTimeInterface <= $allReservationsRelatedToThatParkingSpace->getReservationEndDateTime() || $reservationEndDateTimeInterface > $allReservationsRelatedToThatParkingSpace->getReservationEndDateTime()))){
                return new JsonResponse([
                    'status'=>JsonResponse::HTTP_BAD_REQUEST,
                    'message'=> 'There is already a reservation between the date and time that you choose.'
                ]);
            }else if(($reservationStartDateTimeInterface >= $allReservationsRelatedToThatParkingSpace->getReservationDateTime() && $reservationStartDateTimeInterface <= $allReservationsRelatedToThatParkingSpace->getReservationEndDateTime()) || $reservationStartDateTimeInterface >= $allReservationsRelatedToThatParkingSpace->getReservationDateTime() && $reservationEndDate >= $allReservationsRelatedToThatParkingSpace->getReservationEndDateTime()){
                return new JsonResponse([
                    'status'=>JsonResponse::HTTP_BAD_REQUEST,
                    'message'=> 'There is already a reservation between the date and time that you choose.'
                ]);
            }
        }

        $diff = $reservationStartDateTimeInterface->diff($reservationEndDateTimeInterface);

        $months = "";
        if ($diff->m === 1) {
            $months = $diff->m . ' month';
        } else if ($diff->d > 1) {
            $months = $diff->m . ' months';
        }

        $days = "";
        if ($diff->d === 1) {
            $days = $diff->d . ' day';
        } else if ($diff->d > 1) {
            $days = $diff->d . ' days';
        }

        $hours = "00 hour";
        if ($diff->h === 1) {
            $hours = $diff->h . ' hour';
        } else if ($diff->h > 1) {
            $hours = $diff->h . ' hours';
        }

        $minutes = "00 minutes";
        if ($diff->i === 1) {
            $minutes = $diff->i . ' minute';
        } else if ($diff->i > 1) {
            $minutes = $diff->i . ' minutes';
        }

        $duration = $months . " - " . $days . " - " . $hours . ":" . $minutes;

        $newReservation = new Reservation();

        $newReservation->setUser($findUserConnectedWithToken)
            ->setDuration($duration)
            ->setParkingSpace($findParkingSpaceById)
            ->setReservationDateTime($reservationStartDateTimeInterface)
            ->setReservationEndDateTime($reservationEndDateTimeInterface);

        $this->em->persist($newReservation);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_CREATED,
            'message' => 'Your parking reservation sended successfully.'
        ]);
    }
}
