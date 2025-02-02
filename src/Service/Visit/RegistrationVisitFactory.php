<?php

namespace App\Service\Visit;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Visit;

class RegistrationVisitFactory implements VisitFactoryInterface
{
    public function createVisit(User $user, Event $event, array $data = []): Visit
    {
        return (new Visit())
            ->setVisitor($user)
            ->setEvent($event)
            ->setVisited(false);
    }
}
