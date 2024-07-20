<?php

namespace App\Controller\API;

use App\CodeStatus\CodeStatus;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{

    #[Route('/signup', name: 'signup.users', methods: ['POST'])]
    public function signUp(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        UserRepository $userRepository,
        SendMailService $mail,
        JWTService $jWTService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $confirmPassword = $data['confirm_password'];
        $licencePlate = $data['licence_plate'];

        if (empty($email)) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'Email can not be null.'
            ]);
        }
        if ($email === $password) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'Your email can not be your password. Please change it.'
            ]);
        }

        if (empty($username)) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'Username can not be null.'
            ]);
        }
        if (empty($licencePlate)) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'Licence can not be null.'
            ]);
        }

        if (empty($password)) {
            return new JsonResponse([
                'status' => 'Password can not be null.'
            ]);
        }
        if ($password !== $confirmPassword || empty($confirmPassword)) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'Password does not match. Please confirm it.'
            ]);
        }

        $isEmailExist = $userRepository->findByEmail($email);

        if ($isEmailExist) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => "Email already used. Please change it."
            ]);
        }

        $user = new User();

        $hashPassword = $userPasswordHasherInterface->hashPassword($user, $password);

        $user->setUsername($username)
            ->setEmail($email)
            ->setLicencePlate($licencePlate)
            ->setPassword($hashPassword);

        $em->persist($user);
        $em->flush();

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));



        $mail->send(
            'parking@gmail.com',
            $user->getEmail(),
            'Email confirmation',
            'email-confirmation',
            compact('user', 'token')
        );



        return new JsonResponse([
            'status' => CodeStatus::STATUS_CREATED,
            'message' => 'Your compte is created successfully. Please check your email to confirm your compte.'
        ]);
    }

    #[Route('/verify/{token}', name: 'verify_user')]
    public function verifyUser(
        $token,
        JWTService $jwt,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface
    ): JsonResponse {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {

            $payload = $jwt->getPayload($token);


            $user = $userRepository->find($payload['user_id']);


            if ($user && !$user->isVerified()) {
                $user->setVerified(true);
                $entityManagerInterface->flush($user);
                return new JsonResponse([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'Your account is confirmed successfully.'
                ]);
            }
        }

        return new JsonResponse([
            'status' => JsonResponse::HTTP_BAD_REQUEST,
            'message' => 'Token invalid or expired.'
        ]);
    }
}
