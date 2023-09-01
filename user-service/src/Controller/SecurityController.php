<?php

namespace App\Controller;

use App\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use OpenApi\Annotations as OA;


class SecurityController extends AbstractController
{
    
    /**
     * Authenticates a user and returns a JWT token.
     *
     * Users should send their username and password in the request body.
     * On successful authentication, a JWT token will be returned, 
     * which can be used for subsequent authenticated requests.
     *
     * @OA\Post(
     *     path="/api/login_check",
     *     tags={"Authentication"},
     *     summary="Authenticate a user and retrieve a JWT token",
     *     
     *     @OA\RequestBody(
     *         description="Credentials to authenticate",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", example="john.doe@example.com", description="The user's username/email."),
     *             @OA\Property(property="password", type="string", example="Password123", description="The user's password.")
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="jwt_token_here", description="The JWT token for the authenticated user."),
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request. This can be due to missing credentials.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Missing credentials", description="The error message explaining why the request failed."),
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized. Invalid credentials provided.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials", description="The error message indicating invalid credentials."),
     *         )
     *     )
     * )
     */
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

