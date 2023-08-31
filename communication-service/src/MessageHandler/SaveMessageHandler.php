<?php

namespace App\MessageHandler;

use App\Message\SaveMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\MessageService;

class SaveMessageHandler implements MessageHandlerInterface
{
    private $messageService;

    public function __construct(MessageService $messageService)
    {   
        $this->messageService = $messageService;
    }

    public function __invoke(SaveMessage $message)
    {   
        $this->messageService->createMessage(
            $message->getSenderId(),
            $message->getSenderName(),
            $message->getContent(),
            $message->getAdditionalData()
        );
    }
}

