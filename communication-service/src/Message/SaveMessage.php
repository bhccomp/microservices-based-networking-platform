<?php

namespace App\Message;

class SaveMessage
{
    private $content;
    private $senderId;
    private $senderName;
    private $additionalData;

    public function __construct(string $content, int $senderId, string $senderName, array $additionalData)
    {   
        $this->content = $content;
        $this->senderId = $senderId;
        $this->senderName = $senderName;
        $this->additionalData = $additionalData;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
