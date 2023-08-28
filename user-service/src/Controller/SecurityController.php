<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends AbstractController
{

    /**
     * @Route("/api/login_check", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];

        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user || !$passwordEncoder->isPasswordValid($user, $password)) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
        $jwtToken = $this->container->get('lexik_jwt_authentication.encoder')
            ->encode(
                ['username' => $token->getUser()->getUsername()]
            );

        return new JsonResponse(
            ['token' => $jwtToken]
        );
    }

}
