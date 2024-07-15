<?php

namespace App\Controller\API;

use App\CodeStatus\CodeStatus;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{

    #[Route('/forgot-password', name: 'forgot.password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        JWTService $jwt,
        SendMailService $mailer
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $email = $data['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'The email address is invalid.'
            ]);
        }

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'An error has occured.'
            ]);
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $url = $this->generateUrl('reset.password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $mailer->send(
            'parking@gmail.com',
            $user->getEmail(),
            'Reset password',
            'reset-password',
            compact('user', 'url')
        );

        return new JsonResponse([
            'status' => CodeStatus::STATUS_SUCCESS,
            'message' => 'Please check your email to reset your password.'
        ]);
    }

    #[Route('/reset-password/{token}', name: 'check.token', methods:['GET'])]
    public function checkToken(
        $token,
        JWTService $jwt,

    ): JsonResponse
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {

            return new JsonResponse([
                'status' => CodeStatus::STATUS_SUCCESS,
                'message' => 'You can reset your password.'
            ]);
        }

        return new JsonResponse([
            'status'=> JsonResponse::HTTP_FORBIDDEN,
            'message'=> 'The delay for reset your password is over. Please resend the link.'
        ]);

    }

    #[Route('/reset-password/{token}', name: 'reset.password', methods:['POST'])]
    public function resetPassword(
        $token,
        JWTService $jwt,
        UserRepository $userRepository,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        EntityManagerInterface $entityManagerInterface
    ): JsonResponse {

        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {

            $payload = $jwt->getPayload($token);

            $user = $userRepository->find($payload['user_id']);

            $data = json_decode($request->getContent(), true) ?? null;

            $newPassword = $data['new_password'];
            $confirmNewPassword = $data['confirm_password'];

            if ($newPassword != $confirmNewPassword) {
                return new JsonResponse([
                    'status' => CodeStatus::STATUS_ERROR,
                    'message' => 'Password does not match.'
                ]);
            }

            $hashPassword = $userPasswordHasherInterface->hashPassword(
                $user,
                $newPassword
            );

            $user->setPassword($hashPassword);

            $entityManagerInterface->flush();

            return new JsonResponse([
                'status' => CodeStatus::STATUS_SUCCESS,
                'message' => 'Password reset successfully.'
            ]);
        }
    }
}
