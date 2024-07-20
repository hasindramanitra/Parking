<?php

namespace App\Controller\API;

use App\Entity\Countries;
use App\Repository\CountriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CountriesController extends AbstractController
{
    private $em;

    private $countriesRepository;

    public function __construct(EntityManagerInterface $em, CountriesRepository $countriesRepository)
    {
        $this->em = $em;

        $this->countriesRepository = $countriesRepository;
    }

    #[Route('/countrie-management/countries', name: 'countries.all', methods: ['GET'])]
    public function getAllCountries(): JsonResponse
    {
        $countries = $this->countriesRepository->findAll();

        if (!$countries) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NO_CONTENT,
                'message' => 'No countrie found in database.'
            ]);
        }

        return $this->json([
            'Countries' => $countries
        ], 200, [], [ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object->getId();
        }]);
    }

    #[Route('/countrie-management/countries', name: 'countries.create', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $countrieName = $data['countrie_name'];
        $countrieCode = $data['countrie_code'];

        if (strlen($countrieName) < 3 || empty($countrieName)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie name should not be less than 3 characters.'
            ]);
        }
        if ($countrieCode < 2 || empty($countrieCode)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie Code should not be less 2 characters.'
            ]);
        }

        $newCountrie = new Countries();

        $newCountrie->setCountrieName($countrieName)
            ->setCountrieCode($countrieCode);

        $this->em->persist($newCountrie);
        $this->em->flush();
        return new JsonResponse(
            [
                'status' => JsonResponse::HTTP_CREATED,
                'message' => 'New countrie added successfully.'
            ]
        );
    }

    #[Route('/countrie-management/countries/12434{id}9909', name: 'countries.update', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $countrieName = $data['countrie_name'];
        $countrieCode = $data['countrie_code'];

        if (strlen($countrieName) < 3 || empty($countrieName)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie name should not be less than 3 characters.'
            ]);
        }
        if ($countrieCode < 2 || empty($countrieCode)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie Code should not be less 2 characters.'
            ]);
        }

        $countrieFindById = $this->countriesRepository->find($id);

        if (!$countrieFindById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $countrieFindById->setCountrieName($countrieName)
            ->setCountrieCode($countrieCode);

        $this->em->persist($countrieFindById);
        $this->em->flush();
        return new JsonResponse(
            [
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Countrie updated successfully.'
            ]
        );
    }

    #[Route('/countrie-management/countries/233434{id}445', name: 'countries.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {

        $countrieFindById = $this->countriesRepository->find($id);

        if (!$countrieFindById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $this->em->remove($countrieFindById);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'ressource deleted successfully.'
        ]);
    }
}
