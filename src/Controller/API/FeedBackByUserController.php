<?php
namespace App\Controller\API;

use App\Entity\FeedBackByUser;
use App\Repository\FeedBackByUserRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FeedBackByUserController extends AbstractController
{

    private $feedBackByUserRepository;

    private $em;

    private $tokenStorageInterface;

    private $jwtManager;

    private $userRepository;

    private $reservationRepository;

    public function __construct(
        FeedBackByUserRepository $feedBackByUserRepository,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository,
        ReservationRepository $reservationRepository
    ) 
    {
        $this->feedBackByUserRepository = $feedBackByUserRepository;

        $this->em = $em;

        $this->tokenStorageInterface = $tokenStorageInterface;

        $this->jwtManager = $jwtManager;

        $this->userRepository = $userRepository;

        $this->reservationRepository = $reservationRepository;
    }


    #[Route('/feed-back-management/feed-backs', name: 'feed.back.index', methods:['GET'])]
    public function allFeedBack(

    ): JsonResponse
    {
        $allFeedBack = $this->feedBackByUserRepository->findAll();

        if(!$allFeedBack){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NO_CONTENT,
                'message'=> 'No feed back by user in database.'
            ]);
        }

        return $this->json($allFeedBack);
    }


    #[Route('/api/feed-back-management/feed-backs/54289966{idReservation}34450', name: "feed-back.new", methods:['POST'])]
    public function new(
        Request $request,
        int $idReservation
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $userEmailInToken = $decodedJwtToken['email'];

        $findUserConnectedWithToken = $this->userRepository->findOneByEmail($userEmailInToken);

        if(!$findUserConnectedWithToken){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_UNAUTHORIZED,
                'message'=> 'Please, connect with your account or signup.'
            ]);
        }

        $findReservationById = $this->reservationRepository->find($idReservation);

        if(!$findReservationById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NOT_FOUND,
                'message'=> 'No reservation related that ID.'
            ]);
        }

        $userRelatedToReservation = $findReservationById->getUser();

        if($userRelatedToReservation !== $findUserConnectedWithToken){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_FORBIDDEN,
                'message'=> 'An error has occured.'
            ]);
        }

        $rating = $data['rating'];
        $comments = $data['comments'];

        if(is_string($rating)){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Rating can not be a string, it must be a number.'
            ]);
        }

        if(empty($comments)){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Comments can not be empty.'
            ]);
        }

        $newFeedBackByUser = new FeedBackByUser();

        $newFeedBackByUser->setUser($findUserConnectedWithToken)
            ->setReservation($findReservationById)
            ->setRating($rating)
            ->setComments($comments);

        $this->em->persist($newFeedBackByUser);
        $this->em->flush();

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_CREATED,
            'message'=> 'Thanks for your feed back. It is added successfully.'
        ]);
    }

    #[Route('/api/feed-back-management/feed-backs/54289966{idFeedBack}3445', name: 'edit.feedback', methods:['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        int $idFeedBack
    ):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $userEmailInToken = $decodedJwtToken['email'];

        $findUserConnectedWithToken = $this->userRepository->findOneByEmail($userEmailInToken);

        if(!$findUserConnectedWithToken){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_UNAUTHORIZED,
                'message'=> 'Please, connect with your account or signup.'
            ]);
        }

        $findFeedBackById = $this->feedBackByUserRepository->find($idFeedBack);

        if(!$findFeedBackById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NOT_FOUND,
                'message'=> 'No Feed Back founded with that ID.'
            ]);
        }

        $findUserRelatedThatFeedBack = $findFeedBackById->getUser();

        if($findUserConnectedWithToken !== $findUserRelatedThatFeedBack){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_FORBIDDEN,
                'message'=> 'An error has occured.'
            ]);
        }

        $rating = $data['rating'];
        $comments = $data['comments'];

        if(is_string($rating)){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Rating can not be a string, it must be a number.'
            ]);
        }

        if(empty($comments)){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Comments can not be empty.'
            ]);
        }

        $findFeedBackById->setRating($rating)
            ->setComments($comments);

        $this->em->persist($findFeedBackById);
        $this->em->flush();

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message'=> 'Your feed back is updated successfully.'
        ]);

    }

    #[Route('/api/feed-back-management/feed-backs/54289966{idFeedBack}3445', name: 'delete.feedback', methods:['DELETE'])]
    public function delete(
        int $idFeedBack
    ):JsonResponse
    {
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $userEmailInToken = $decodedJwtToken['email'];

        $findUserConnectedWithToken = $this->userRepository->findOneByEmail($userEmailInToken);

        if(!$findUserConnectedWithToken){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_UNAUTHORIZED,
                'message'=> 'Please, connect with your account or signup.'
            ]);
        }

        $findFeedBackById = $this->feedBackByUserRepository->find($idFeedBack);

        if(!$findFeedBackById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NOT_FOUND,
                'message'=> 'No Feed Back founded with that ID.'
            ]);
        }

        $findUserRelatedThatFeedBack = $findFeedBackById->getUser();

        if($findUserConnectedWithToken !== $findUserRelatedThatFeedBack){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_FORBIDDEN,
                'message'=> 'An error has occured.'
            ]);
        }

        $this->em->remove($findFeedBackById);
        $this->em->flush();

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message'=> 'Your feed back has deleted successfully.'
        ]);
    }
}