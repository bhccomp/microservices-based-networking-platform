<?php

namespace App\Controller;

use App\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/login_check', name: 'login', methods: ['POST'])]
    public function login(
        Request $request, 
        AuthenticationService $authenticationService
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (null === $username || null === $password) {
            return new JsonResponse(
                ['error' => 'Missing credentials'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $jwtToken = $authenticationService->getJWTForUser($username, $password);

        if (null === $jwtToken) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        return new JsonResponse(['token' => $jwtToken]);
    }
}

