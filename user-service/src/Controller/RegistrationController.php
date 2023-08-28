<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/api/register", name="app_register", methods={"POST"})
     */
    public function register(Request $request, UserRepository $userRepository, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return new JsonResponse(
                ['error' => 'Missing parameters'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        $existingUser = $userRepository->findOneByEmail($data['email']);
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'Email already in use'], 
                Response::HTTP_CONFLICT
            );
        }
        
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');
        $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(
                json_encode($errorMessages), 
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            ['success' => 'User registered successfully'], 
            Response::HTTP_CREATED
        );
    }
}
