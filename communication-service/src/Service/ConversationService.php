<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConversationService
{
    private $conversationRepository;
    private $em;

    public function __construct(ConversationRepository $conversationRepository, EntityManagerInterface $em)
    {
        $this->conversationRepository = $conversationRepository;
        $this->em = $em;
    }

    public function getUserConversations(int $userId): array
    {
        $qb = $this->conversationRepository->createQueryBuilder('c');
        return $qb
            ->where($qb->expr()->orX('c.user1_id = :userId', 'c.user2_id = :userId'))
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function getUserConversation(int $id, int $userId): ?Conversation
    {
        return $this->conversationRepository->findOneBy([
            'id' => $id,
            'user1_id' => $userId
        ]) ?? $this->conversationRepository->findOneBy([
            'id' => $id,
            'user2_id' => $userId
        ]);
    }

    public function createConversation(int $user1Id, int $user2Id): Conversation
    {
        $conversation = new Conversation();
        $conversation->setUser1Id($user1Id);
        $conversation->setUser2Id($user2Id);
        $conversation->setStartedAt(new \DateTime());

        $this->em->persist($conversation);
        $this->em->flush();

        return $conversation;
    }

    public function deleteConversation(Conversation $conversation): void
    {
        $this->em->remove($conversation);
        $this->em->flush();
    }
}
