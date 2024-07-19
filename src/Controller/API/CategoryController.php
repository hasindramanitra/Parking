<?php

namespace App\Controller\API;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    private $em;

    private $categorieRepository;

    public function __construct(EntityManagerInterface $em, CategoryRepository $categorieRepository)
    {
        $this->em = $em;

        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/category-management/categories', name: 'categories.all', methods: 'GET')]
    public function getAllCategories(
        SerializerInterface $serializerInterface
    ): JsonResponse {
        $categories = $this->categorieRepository->findAll();

        if (!$categories) {

            return new JsonResponse([
                'status' => JsonResponse::HTTP_NO_CONTENT,
                'message' => 'No category found in database.'
            ]);
        }

        $jsonCategories = $serializerInterface->serialize($categories, 'json');
        return new JsonResponse($jsonCategories, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/category-management/categories', name: 'categories.create', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $categoryName = $data['category_name'];
        $categoryLength = $data['category_length'];
        $categoryWidth = $data['category_width'];

        if (strlen($categoryName) < 4 || empty($categoryName)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Category name should not be less than 4 characters.'
            ]);
        }
        if ($categoryLength < 0 || $categoryLength === null) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Category length should not be less or equal 0.'
            ]);
        }

        if ($categoryWidth < 0 || $categoryWidth === null) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Category width should not be less or equal 0.'
            ]);
        }

        $newCategory = new Category();

        $newCategory->setName($categoryName)
            ->setLength($categoryLength)
            ->setWidth($categoryWidth);

        $this->em->persist($newCategory);
        $this->em->flush();
        return new JsonResponse(
            [
                'status' => JsonResponse::HTTP_CREATED,
                'message' => 'New category added successfully.'
            ]
        );
    }

    #[Route('/categories/12434{id}9909JK', name: 'categories.update', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $categoryName = $data['category_name'];
        $categoryLength = $data['category_length'];
        $categoryWidth = $data['category_width'];

        if (strlen($categoryName) < 4 || empty($categoryName)) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Category name should not be less than 4 characters.'
            ]);
        }
        if ($categoryLength < 0 || $categoryLength === null) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Category length should not be less or equal 0.'
            ]);
        }

        if ($categoryWidth < 0 || $categoryWidth === null) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Category width should not be less or equal 0.'
            ]);
        }

        $categoryFindById = $this->categorieRepository->find($id);

        if (!$categoryFindById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $categoryFindById->setName($categoryName)
            ->setLength($categoryLength)
            ->setWidth($categoryWidth);

        $this->em->persist($categoryFindById);
        $this->em->flush();
        return new JsonResponse(
            [
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Category updated successfully.'
            ]
        );
    }

    #[Route('/categories/233434{id}445', name: 'category.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {

        $categoryFindById = $this->categorieRepository->find($id);

        if (!$categoryFindById) {
            return new JsonResponse([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'ressources not found.'
            ]);
        }

        $this->em->remove($categoryFindById);
        $this->em->flush();

        return new JsonResponse([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'ressource deleted successfully.'
        ]);
    }
}
