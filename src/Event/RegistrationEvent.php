<?php

namespace App\Event;

use App\Entity\Event as EventEntity;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class RegistrationEvent extends Event
{
    public function __construct(
        private readonly User        $user,
        private readonly EventEntity $event
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEvent(): EventEntity
    {
        return $this->event;
    }
}
