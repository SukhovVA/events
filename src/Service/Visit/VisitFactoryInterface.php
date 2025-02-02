<?php

namespace App\Service\Visit;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Visit;

interface VisitFactoryInterface
{
    public function createVisit(User $user, Event $event, array $data = []): Visit;
}
