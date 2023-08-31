<?php 

namespace App\MessageHandler;

use App\Message\EmailNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationHandler implements MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(EmailNotification $notification)
    {
        if ($notification->getType() === 'WELCOME_EMAIL') {
            $email = (new Email())
                ->from('hello@social-network.com')
                ->to($notification->getEmail())
                ->subject('Welcome to Our Platform')
                ->text(sprintf('Hello %s, welcome to our platform!', $notification->getData()['name']));

            $this->mailer->send($email);
        }

    }
}
