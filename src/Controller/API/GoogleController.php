<?php
namespace App\Controller\API;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{

    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(
        ClientRegistry $clientRegistry
    )
    {
        return $clientRegistry->getClient('google')
            ->redirect([],[
                'public_profile', 'email'
            ]);
    }


    #[Route('/connect/google/check', name: "connect_google_check")]
    public function connectCheckAction(
        Request $request,
        ClientRegistry $clientRegistry
    ):JsonResponse
    {
        $client = $clientRegistry->getClient('google');

        try{
            $user = $client->fetchUser();
        }catch(IdentityProviderException $e){
            var_dump($e->getMessage()); die;
        }


        return new JsonResponse([
            'status'=> JsonResponse::HTTP_OK,
            'message'=> 'Authenticated successfully with Google.'
        ]);
    }
}