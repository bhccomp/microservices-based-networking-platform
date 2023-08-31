<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailController extends AbstractController
{
    #[Route('/email', name: 'app_email')]
    public function index(MailerInterface $mailer): JsonResponse
    {
        $email = (new Email())
        ->from('admin@social-network.com')
        ->to('user@example.com')
        ->subject('Test Email')
        ->text('Sending a simple email using MailHog.');

        $mailer->send($email);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/EmailController.php',
        ]);
    }
}

