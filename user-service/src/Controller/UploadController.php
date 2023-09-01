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

        $minioEndpoint = "localhost:9000"; //$this->params->get('MINIO_ENDPOINT');
        $minioBucket = "profile-images"; //$this->params->get('MINIO_BUCKET');
        $imageUrl = "http://" . $minioEndpoint . "/" . $minioBucket . "/" . $filename;

        $this->registrationService->updateProfileImagePath($userId, $imageUrl);

        return new JsonResponse(['filename' => $imageUrl], JsonResponse::HTTP_OK);
    }
}

