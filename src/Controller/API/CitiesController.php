<?php
namespace App\Controller\API;

use App\Entity\Cities;
use App\Repository\CitiesRepository;
use App\Repository\CountriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CitiesController extends AbstractController
{
    private $em;

    private $citiesRepository;

    private $countrieRepository;

    public function __construct(EntityManagerInterface $em, CitiesRepository $citiesRepository, CountriesRepository $countrieRepository)
    {
        $this->em = $em;

        $this->citiesRepository = $citiesRepository;

        $this->countrieRepository = $countrieRepository;
    }

   #[Route('/citie-management/cities', name: 'cities.all', methods: ['GET'])]
    public function getAllCities(
    ): JsonResponse
    {
        $cities = $this->citiesRepository->findAll();

        if(!$cities){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NO_CONTENT,
                'message'=> 'No city found in database.'
            ]);
        }

        $AllCities = [];
        foreach($cities as $citie){

            $AllCities[] =[
                'id'=> $citie->getId(),
                'citieName'=> $citie->getCitieName(),
                'citieCode'=> $citie->getCitieCode(),
                'countrie'=> $citie->getCountries()?->getCountrieName()
            ];
        }

        return $this->json(['cities'=>$AllCities], 200);

        // return $this->json([
        //     'Cities'=>$cities
        // ], 200, [], [ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function($object){
        //     return $object->getId();
        // }]);
    }

    #[Route('/citie-management/cities', name: 'cities.create', methods:'POST')]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $citieName = $data['citie_name'];
        $citieCode = $data['citie_code'];
        $countrieId = $data['countrie_id'];

        if($countrieId === null){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=>'Countrie ID can not be null.'
            ]);
        }

        $countrieFindById = $this->countrieRepository->find($countrieId);

        if(!$countrieFindById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=>'No countrie found with that ID.'
            ]);
        }

        if (strlen($citieName) < 3 || empty($citieName)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie name should not be less than 3 characters.'
            ]);
        }
        if ($citieCode < 2 || empty($citieCode)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie Code should not be less 2 characters.'
            ]);
        }

        $newCitie = new Cities();

        $newCitie->setcitieName($citieName)
            ->setcitieCode($citieCode)
            ->setCountries($countrieFindById);

        $this->em->persist($newCitie);
        $this->em->flush();
        return new JsonResponse([
            'status'=> JsonResponse::HTTP_CREATED,
            'message' => 'New countrie added successfully.'
        ]
        );
    }

    #[Route('/citie-management/cities/12434{id}9909', name: 'cities.update', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $citieName = $data['citie_name'];
        $citieCode = $data['citie_code'];
        $countrieId = $data['countrie_id'];

        if($countrieId === null){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=>'Countrie ID can not be null.'
            ]);
        }

        $countrieFindById = $this->countrieRepository->find($countrieId);

        if(!$countrieFindById){

            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=>'No countrie found with that ID.'
            ]);
        }

        if (strlen($citieName) < 3 || empty($citieName)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie name should not be less than 3 characters.'
            ]);
        }
        if ($citieCode < 2 || empty($citieCode)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Countrie Code should not be less 2 characters.'
            ]);
        }

        $citieFindById = $this->citiesRepository->find($id);

        if(!$citieFindById){
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $citieFindById->setcitieName($citieName)
            ->setcitieCode($citieCode)
            ->setCountries($countrieFindById);

        $this->em->persist($citieFindById);
        $this->em->flush();
        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message' => 'Citie updated successfully.'
        ]
        );
    }

    #[Route('/citie-management/cities/233434{id}445', name: 'cities.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {

        $citieFindById = $this->citiesRepository->find($id);

        if(!$citieFindById){
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $this->em->remove($citieFindById);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'ressource deleted successfully.'
        ]);
    }
}