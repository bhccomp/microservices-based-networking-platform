<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RegistrationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Areas;
use OpenApi\Annotations as OA;

class RegistrationController
{
    private $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * Registers a new user.
     *
     * Create a new user account by providing email and password. 
     * Upon successful registration, the user details will be returned.
     *
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Registration"},
     *     summary="Register a new user",
     *     
     *     @OA\RequestBody(
     *         description="User data to register",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="example@example.com", description="The email of the user."),
     *             @OA\Property(property="password", type="string", example="password123", description="The desired password for the account."),
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(ref=@Model(type=App\Entity\User::class))
     *     ),
     *     
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request. This can be due to missing parameters or registration failure.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Missing parameters", description="The error message explaining why the request failed."),
     *         )
     *     )
     * )
     */
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
