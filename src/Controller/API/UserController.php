<?php
namespace App\Controller\API;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private $em;

    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository) {

        $this->em = $em;

        $this->userRepository = $userRepository;
        
    }

    #[Route('/users-management/users', name: 'users.all', methods: ['GET'])]
    public function getAllUsers(
    ): JsonResponse
    {
        $users = $this->userRepository->findAll();

        if(!$users){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NO_CONTENT,
                'message'=> 'No users found in database.'
            ]);
        }

        $allUsers = [];

        foreach($users as $user){

            $allUsers[] = [
                'id'=> $user->getId(),
                'email'=> $user->getEmail(),
                'username'=> $user->getUsername(),
                'licencePlate'=> $user->getLicencePlate()
            ];
        }

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'users'=> $allUsers
        ]);
    }


    #[Route('/users-management/users/1223{id}8979', name: 'users.edit', methods:['PUT', 'PATCH'])]
    public function edit(
        int $id,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface

    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $licencePlate = $data['licence_plate'];
        $password = $data['password'];

        $findUserById = $this->userRepository->find($id);

        if(!$findUserById){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NOT_FOUND,
                'message'=> 'User not found in database.'
            ]);
        }

        if (empty($username)) {
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Username can not be null.'
            ]);
        }
        if(empty($licencePlate)){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Licence can not be null.'
            ]);
        }

        $hashPassword = $userPasswordHasherInterface->isPasswordValid($findUserById, $password);

        if(!$hashPassword){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_BAD_REQUEST,
                'message'=> 'Invalid password.'
            ]);
        }

        $findUserById->setUsername($username)
            ->setLicencePlate($licencePlate);

        $this->em->persist($findUserById);
        $this->em->flush();

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message'=> 'Informations updated successfully.'
        ]); 
    }

    #[Route('/users-management/users/1223{id}8979', name: 'users.delete', methods:['DELETE'])]
    public function deleteUser(
        int $id
    ):JsonResponse
    {
        $findUserById = $this->userRepository->find($id);

        if(!$findUserById){
            return new JsonResponse([
                'status'=> JsonResponse::HTTP_NOT_FOUND,
                'message'=> 'User not found or already deleted.'
            ]);
        }

        $this->em->remove($findUserById);
        $this->em->flush();

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message'=> 'User deleted successfully.'
        ]);
    }
}