<?php

namespace App\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private $messageRepository;
    private $em;

    public function __construct(MessageRepository $messageRepository, EntityManagerInterface $em)
    {
        $this->messageRepository = $messageRepository;
        $this->em = $em;
    }

    public function getUserMessages(int $userId): array
    {
        return $this->messageRepository->findBy(['sender_id' => $userId]);
    }

    public function getUserMessage(int $id, int $userId): ?Message
    {
        return $this->messageRepository->findOneBy([
            'id' => $id,
            'sender_id' => $userId
        ]);
    }

    public function createMessage(string $senderId, string $senderName, string $messageContent, array $additionalData): Message
    {   
        
        $message = new Message();
        $message->setContent($messageContent);
        $message->setSenderId($senderId);
        $message->setSenderName($senderName);
        $message->setCreatedAt(new \DateTime());

        if (!empty($additionalData['parentMessage'])) {
            $message->setParentMessage($additionalData['parentMessage']);
        }

        if (!empty($additionalData['conversation_id'])) {
            $message->setConversationId($additionalData['conversation_id']);
        }
        
        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

}
