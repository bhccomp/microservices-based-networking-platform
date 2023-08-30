<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RegistrationService;

class RegistrationController
{
    private $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    #[Route(path: '/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return new JsonResponse(
                ['error' => 'Missing parameters'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->registrationService->registerUser($data);
            return new JsonResponse(
                ['success' => 'User registered successfully'], 
                Response::HTTP_CREATED
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()], 
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
