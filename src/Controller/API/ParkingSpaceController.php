<?php

namespace App\Controller\API;

use App\Entity\ParkingSpace;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParkingFloorRepository;
use App\Repository\ParkingSpaceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParkingSpaceController extends AbstractController
{

    private $em;

    private $parkingSpaceRepository;

    public function __construct(EntityManagerInterface $em, ParkingSpaceRepository $parkingSpaceRepository)
    {

        $this->em = $em;

        $this->parkingSpaceRepository = $parkingSpaceRepository;
    }

    #[Route('/parking-space-management/parking-spaces', name: 'parking-spaces.all', methods: ['GET'])]
    public function getAllParkingSpace(): JsonResponse
    {
        $parkingSpaces = $this->parkingSpaceRepository->findAll();

        if (!$parkingSpaces) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NO_CONTENT,
                'message' => 'No parking spaces found in database.'
            ]);
        }

        $allParkingSpaces = [];

        foreach ($parkingSpaces as $parkingSpace) {

            $allParkingSpaces[] = [
                'id' => $parkingSpace->getId(),
                'isAvailable' => $parkingSpace->isAvalaibilityStatus(),
                'parkingSpaceRating' => $parkingSpace->getRate(),
                'category' => $parkingSpace->getCategorie()?->getName(),
                'parkingFloor' => $parkingSpace->getParkingFloor()->getNomination()
            ];
        }

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'parkingSpaces' => $allParkingSpaces
        ]);
    }

    #[Route('/parking-space-management/parking-spaces', name: 'parking-spaces.new', methods: ['POST'])]
    public function new(
        Request $request,
        ParkingFloorRepository $parkingFloorRepository,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $availableStatus = $data['available'];

        $parkingSpaceRate = $data['rate'];

        $categoryId = $data['category_id'];

        $parkingFloorId = $data['parking_floor_id'];

        $findCategoryById = $categoryRepository->find($categoryId);

        if (!$findCategoryById) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Category not found.'
            ]);
        }

        $findParkingFloorById = $parkingFloorRepository->find($parkingFloorId);

        if (!$findParkingFloorById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Parking floor not found.'
            ]);
        }

        if (!is_bool($availableStatus)) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Available status must be a boolean value.'
            ]);
        }

        if ($parkingSpaceRate <= 0) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'The rate of parking space must be positive.'
            ]);
        }

        $newParkingSpace = new ParkingSpace();

        $newParkingSpace->setAvalaibilityStatus($availableStatus)
            ->setRate($parkingSpaceRate)
            ->setCategorie($findCategoryById)
            ->setParkingFloor($findParkingFloorById);

        $this->em->persist($newParkingSpace);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_CREATED,
            'message' => 'Parking space added successfully.'
        ]);
    }

    #[Route('/parking-space-management/parking-spaces/12434{id}9909', name: 'parking-spaces.update', methods: ['PUT', 'PATCH'])]
    public function edit(
        int $id,
        Request $request,
        ParkingFloorRepository $parkingFloorRepository,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $availableStatus = $data['available'];

        $parkingSpaceRate = $data['rate'];

        $categoryId = $data['category_id'];

        $parkingFloorId = $data['parking_floor_id'];

        $findCategoryById = $categoryRepository->find($categoryId);

        if (!$findCategoryById) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Category not found.'
            ]);
        }

        $findParkingFloorById = $parkingFloorRepository->find($parkingFloorId);

        if (!$findParkingFloorById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Parking floor not found.'
            ]);
        }

        if (!is_bool($availableStatus)) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Available status must be a boolean value.'
            ]);
        }

        if ($parkingSpaceRate <= 0) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'The rate of parking space must be positive.'
            ]);
        }

        $findParkingSpaceById = $this->parkingSpaceRepository->find($id);

        if (!$findParkingSpaceById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $findParkingSpaceById->setAvalaibilityStatus($availableStatus)
            ->setRate($parkingSpaceRate)
            ->setCategorie($findCategoryById)
            ->setParkingFloor($findParkingFloorById);

        $this->em->persist($findParkingSpaceById);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Parking space updated successfully.'
        ]);
    }

    #[Route('/parking-space-management/parking-spaces/12434{id}9909', name: 'parking-spaces.delete', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse {
        $findParkingSpaceById = $this->parkingSpaceRepository->find($id);

        if (!$findParkingSpaceById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $this->em->remove($findParkingSpaceById);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'ressources deleted successfully.'
        ]);
    }
}
