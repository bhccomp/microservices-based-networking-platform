<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class AuthenticationService
{
    private $userRepository;
    private $passwordEncoder;
    private $jwtEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTEncoderInterface $jwtEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function getJWTForUser(string $username, string $password): ?string
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return null;
        }

        $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());

        return $this->jwtEncoder->encode(
            ['username' => $token->getUser()->getUsername()]
        );
    }
}
