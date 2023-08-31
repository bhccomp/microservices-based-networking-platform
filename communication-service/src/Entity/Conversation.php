<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $started_at = null;

    #[ORM\Column]
    private ?int $user1Id = null;

    #[ORM\Column]
    private ?int $user2Id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeInterface $started_at): static
    {
        $this->started_at = $started_at;
        return $this;
    }

    public function getUser1Id(): ?int
    {
        return $this->user1Id;
    }

    public function setUser1Id(int $user1Id): static
    {
        $this->$user1Id = $user1Id;
        return $this;
    }

    public function getUser2Id(): ?int
    {
        return $this->user2Id;
    }

    public function setUser2Id(int $user2Id): static
    {
        $this->user2Id = $user2Id;
        return $this;
    }
}
