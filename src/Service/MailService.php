<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string          $fromEmail,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendRegistrationEmail(string $email): void
    {
        $email = $this
            ->getEmailInstance()
            ->to($email)
            ->subject('Регистрация на мероприятие')
            ->text('Вы успешно зарегистрированы на мероприятие')
        ;

        $this->mailer->send($email);
    }

    private function getEmailInstance(): Email
    {
        return (new Email())->from($this->fromEmail);
    }
}
