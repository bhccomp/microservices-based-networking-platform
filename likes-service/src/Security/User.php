<?php 

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class User implements UserInterface, JWTUserInterface
{   
    private $id;
    private $email;
    private $roles;
    private $firstName;
    private $lastName;

    public function __construct($id, $email, array $roles, $firstName, $lastName)
    {   
        $this->id = $id;
        $this->email = $email;
        $this->roles = $roles;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public static function createFromPayload($username, array $payload)
    {
        $id = $payload['id'] ?? 0;
        $firstName = $payload['first_name'] ?? [];
        $lastName = $payload['last_name'] ?? '';
        $email = $payload['username'] ?? '';
        $roles = $payload['roles'] ?? [];

        return new self($id, $email, $roles, $firstName, $lastName);
    }

}
