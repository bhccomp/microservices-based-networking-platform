<?php

namespace App\Controller;

use App\Service\ProfileImageUploadService;
use App\Service\RegistrationService;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use OpenApi\Annotations as OA;

class UploadController
{
    private $uploadService;
    private $security;
    private $registrationService;
    private $params;

    public function __construct(
        ProfileImageUploadService $uploadService, 
        Security $security, 
        RegistrationService $registrationService,
        ParameterBagInterface $params
    )
    {
        $this->uploadService = $uploadService;
        $this->security = $security;
        $this->registrationService = $registrationService;
        $this->params = $params;
    }

    /**
     * Uploads a user's profile image.
     *
     * Allows the authenticated user to upload a profile image. 
     * On successful upload, the profile image's URL will be returned.
     *
     * @OA\Post(
     *     path="/api/upload/profile-image",
     *     tags={"Profile Management"},
     *     summary="Upload a user profile image",
     *     
     *     @OA\RequestBody(
     *         description="Profile image to upload",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image",
     *                     description="The profile image to upload",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Profile image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="filename", type="string", example="http://localhost:9000/profile-images/example.jpg", description="The URL to the uploaded profile image."),
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request. This can be due to missing image file.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No file provided", description="The error message explaining why the request failed."),
     *         )
     *     )
     * )
     */
    #[Route(path: '/api/upload/profile-image', name: 'upload_profile_image', methods: ['POST'])]
    public function uploadProfileImage(Request $request): JsonResponse
    {
        $file = $request->files->get('image');

        if (!$file) {
            return new JsonResponse(['error' => 'No file provided'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $filename = $this->uploadService->upload($file);
        
        $user = $this->security->getUser();
        $userId = $user->getId();

        // hardcoded until I figure out why I can't use ENV variables. :) 
        $minioEndpoint = "localhost:9000"; //$this->params->get('MINIO_ENDPOINT');
        $minioBucket = "profile-images"; //$this->params->get('MINIO_BUCKET');
        $imageUrl = "http://" . $minioEndpoint . "/" . $minioBucket . "/" . $filename;

        $this->registrationService->updateProfileImagePath($userId, $imageUrl);

        return new JsonResponse(['filename' => $imageUrl], JsonResponse::HTTP_OK);
    }
}

