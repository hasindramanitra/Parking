<?php

namespace App\Controller\API;

use App\Entity\Parks;
use App\Repository\CitiesRepository;
use App\Repository\ParksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class ParksController extends AbstractController
{

    private $em;

    private $parksRepository;

    public function __construct(EntityManagerInterface $em, ParksRepository $parksRepository)
    {

        $this->em = $em;

        $this->parksRepository = $parksRepository;
    }

    #[Route('/parkings-management/parkings/{id}', name: 'parking.by.countrie', methods:['POST'])]
    public function getParkingsByCountrie(
        int $id
    ):JsonResponse
    {

        $parkings = $this->parksRepository->findAllParkingsByCountrieWithCountrieId($id);
        if (!$parkings) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NO_CONTENT,
                'message' => 'No parking found in database.'
            ]);
        }

        $allParkings = [];
        foreach($parkings as $parking){
            $allParkings[] = [
                'id'=> $parking->getId(),
                'location'=> $parking->getLocation(),
                'citie'=> $parking->getCities()?->getCitieName(),
                'countrie'=>$parking->getCities()->getCountries()->getCountrieName()
            ];
        }
        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'parkings'=> $allParkings
        ]);
    }

    #[Route('/parkings-management/parkings', name: 'parkings.all', methods: ['GET'])]
    public function getAllParks(
    ): JsonResponse
    {
        $parkings = $this->parksRepository->findAll();


        if (!$parkings) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NO_CONTENT,
                'message' => 'No parking found in database.'
            ]);
        }

         $allParkings = [];
         foreach($parkings as $parking){
             $allParkings[] = [
                 'id'=> $parking->getId(),
                 'location'=> $parking->getLocation(),
                 'citie'=> $parking->getCities()?->getCitieName()
             ];
         }
         return new JsonResponse([
             'status'=> JsonResponse::HTTP_OK,
             'parkings'=> $allParkings
         ]);
    }

    #[Route('/parkings-management/parkings', name: 'parkings.create', methods: 'POST')]
    public function new(Request $request, CitiesRepository $citiesRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $parksLocation = $data['location'];
        $citieId = $data['citie_id'];

        $findCitieById = $citiesRepository->find($citieId);

        if (!$findCitieById) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'City not found.'
            ]);
        }

        if (empty($parksLocation)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Parking Location can not be empty.'
            ]);
        }

        $newParks = new Parks();

        $newParks->setLocation($parksLocation)
            ->setCities($findCitieById);

        $this->em->persist($newParks);
        $this->em->flush();
        return new JsonResponse(
            [
                'status' => JsonResponse::HTTP_CREATED,
                'message' => 'New parking added successfully.'
            ]
        );
    }

    #[Route('/parkings-management/parkings/12434{id}9909', name: 'parkings.update', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        int $id,
        ParksRepository $parksRepository,
        CitiesRepository $citiesRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $parksLocation = $data['location'];
        $citieId = $data['citie_id'];

        $findParkById = $parksRepository->find($id);

        $findCitieById = $citiesRepository->find($citieId);

        if (!$findCitieById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'City not found.'
            ]);
        }

        if (!$findParkById) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        if (empty($parksLocation)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Parking location can not be empty.'
            ]);
        }

        $findParkById->setLocation($parksLocation)
            ->setCities($findCitieById);

        $this->em->persist($findParkById);
        $this->em->flush();

        return new JsonResponse(
            [
                'status' => JsonResponse::HTTP_OK,
                'message' => 'parking updated successfully.'
            ]
        );
    }

    #[Route('/parkings-management/parkings/12434{id}9909', name: 'parkings.delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        ParksRepository $parksRepository
    ): JsonResponse {

        $findParkById = $parksRepository->find($id);

        if (!$findParkById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $this->em->remove($findParkById);
        $this->em->flush();


        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'ressource deleted successfully.'
        ]);
    }
}
