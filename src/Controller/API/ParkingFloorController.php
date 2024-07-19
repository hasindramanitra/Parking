<?php
namespace App\Controller\API;

use App\Entity\ParkingFloor;
use App\Repository\ParkingFloorRepository;
use App\Repository\ParksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParkingFloorController extends AbstractController
{
    private $em;

    private $parkingFloorRepository;

    public function __construct(EntityManagerInterface $em, ParkingFloorRepository $parkingFloorRepository)
    {
        $this->em = $em;

        $this->parkingFloorRepository = $parkingFloorRepository;
    }

   #[Route('/parking-floor-management/parking-floors', name: 'parking-floors.all', methods: ['GET'])]
    public function getAllParkingFloor(

    ): JsonResponse
    {
        $parkingFloors = $this->parkingFloorRepository->findAll();

        if(!$parkingFloors){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NO_CONTENT,
                'message'=> 'No parking floor found in database.'
            ]);
        }

        $allParkingFloors = [];

        foreach($parkingFloors as $parkingFloor){

            $allParkingFloors[] = [
                'id'=> $parkingFloor->getId(),
                'nomination'=> $parkingFloor->getNomination(),
                'parkingSpaces'=> $parkingFloor->getParkingSpaces()
            ];
        }


        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'parkingFloors' => $allParkingFloors
        ]);
    }

    #[Route('/parking-floor-management/parking-floors', name: 'parking-floors.create', methods:'POST')]
    public function new(Request $request, ParksRepository $parksRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $parkingFloorNomination = $data['nomination'];
        $parkId = $data['park_id'];

        $findParkById = $parksRepository->find($parkId);

        if(empty($parkingFloorNomination)){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Parking Floor nomination can not be empty.'
            ]);
        }

        if(!$findParkById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'parking not found.'
            ]);
        }

        

        $newParkingFloor = new ParkingFloor();

        $newParkingFloor->setNomination($parkingFloorNomination)
            ->setParks($findParkById);

        $this->em->persist($newParkingFloor);
        $this->em->flush();
        return new JsonResponse([
            'status'=> JsonResponse::HTTP_CREATED,
            'message' => 'New parking floor added successfully.'
        ]
        );
    }

    #[Route('/parking-floor-management/parking-floors/12434{id}9909', name: 'parking-floors.update', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, int $id, ParksRepository $parksRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $parkingFloorNomination = $data['nomination'];
        $parkId = $data['park_id'];

        $findParkById = $parksRepository->find($parkId);

        if(!$findParkById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'parking not found.'
            ]);
        }

        if(empty($parkingFloorNomination)){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Parking Floor nomination can not be empty.'
            ]);
        }

        $findParkingFloorById = $this->parkingFloorRepository->find($id);

        if(!$findParkingFloorById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'ressources not found.'
            ]);
        }

        $findParkingFloorById->setNomination($parkingFloorNomination)
            ->setParks($findParkById);

        $this->em->persist($findParkingFloorById);
        $this->em->flush();

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message' => 'parking floor updated successfully.'
        ]
        );
    }

    #[Route('/parking-floor-management/parking-floors/233434{id}445', name: 'parking-floors.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {

        $findParkingFloorById = $this->parkingFloorRepository->find($id);

        if(!$findParkingFloorById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'ressources not found.'
            ]);
        }

        $this->em->remove($findParkingFloorById);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'ressource deleted successfully.'
        ]);
    }
}