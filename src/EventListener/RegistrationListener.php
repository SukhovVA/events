<?php

namespace App\EventListener;


use App\Event\RegistrationEvent;
use App\Service\MailService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsEventListener(event: RegistrationEvent::class, method: 'onVisitRegistered')]
readonly class RegistrationListener
{
    public function __construct(private MailService $mailService) {}

    /**
     * @throws TransportExceptionInterface
     */
    #[AsEventListener(event: RegistrationEvent::class)]
    public function onVisitRegistered(RegistrationEvent $event): void
    {
        $this->mailService->sendRegistrationEmail($event->getUser()->getEmail());
    }
}
