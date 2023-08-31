<?php

namespace App\EventListener;

use App\Message\EmailNotification;
use App\Event\UserRegisteredEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class UserRegistrationListener
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $user = $event->getUser();
        $type = "WELCOME_EMAIL";
        $data['name'] = $user->getFirstName();
        
        $message = new EmailNotification($user->getEmail(), $type, $data);

        $this->bus->dispatch($message);
    }
}

