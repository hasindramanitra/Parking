<?php
namespace App\Controller\API;

use App\CodeStatus\CodeStatus;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{

    #[Route('/contact', name:'contact', methods:['POST'])]
    public function contact(
        Request $request,
        MailerInterface $mailer

    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $email = $data['email'];
        $subject = $data['subject'];
        $message = $data['message'];

        if(strlen($firstname) < 3 || $firstname === '' ){
            return new JsonResponse([
                'status'=> CodeStatus::STATUS_ERROR,
                'message'=> 'Firstname can not be null or less than 3 words.'
            ]);
        }
        if(strlen($lastname) < 3 || $lastname === '' ){
            return new JsonResponse([
                'status'=> CodeStatus::STATUS_ERROR,
                'message'=> 'Lastname can not be null or less than 3 words.'
            ]);
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return new JsonResponse([
                'status'=> CodeStatus::STATUS_ERROR,
                'message'=> 'The email address is invalid.'
            ]);
        }

        if(strlen($subject) < 3 || $subject === ''){
            return new JsonResponse([
                'status'=> CodeStatus::STATUS_ERROR,
                'message'=> 'Subject can not be null or less than 3 words.'
            ]);
        }

        if(strlen($message) < 10 || $message === ''){
            return new JsonResponse([
                'status' => CodeStatus::STATUS_ERROR,
                'message' => 'Message can not be null or less than 10 words.'
            ]);
        }

        $mail = (new TemplatedEmail())
            ->to('parking@gmail.com')
            ->from($email)
            ->subject($subject)
            ->htmlTemplate('email/contact.html.twig')
            ->context(['data' => $data]);

        $mailer->send($mail);

        return new JsonResponse([
            'status' => CodeStatus::STATUS_SUCCESS,
            'message' => 'Your email sended successfully.'
        ]);
    }
}