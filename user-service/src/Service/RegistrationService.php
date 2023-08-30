<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationService
{
    private $entityManager;
    private $passwordEncoder;
    private $validator;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordEncoder, 
        ValidatorInterface $validator,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data): User
    {
        $existingUser = $this->userRepository->findOneByEmail($data['email']);
        if ($existingUser) {
            throw new \InvalidArgumentException("Email already in use.");
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');
        $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));

        $errors = $this->validator->validate($user);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \InvalidArgumentException(implode(', ', $errorMessages));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
