<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {   
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {   
        
        $request = $this->requestStack->getCurrentRequest();

        $user = $event->getUser();
        $payload = $event->getData();

        if (method_exists($user, 'getId')) {
            $payload['id'] = $user->getId();
        }
        
        if (method_exists($user, 'getFirstName')) {
            $payload['first_name'] = $user->getFirstName();
        }

        if (method_exists($user, 'getLastName')) {
            $payload['last_name'] = $user->getLastName();
        }

        $event->setData($payload);

    }

}