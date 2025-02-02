<?php

namespace App\Service\Visit;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Visit;
use App\Event\RegistrationEvent;
use App\Exception\VisitExistException;
use App\Repository\VisitRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class VisitService
{
    public function __construct(
        private readonly VisitRepository          $visitRepository,
        private readonly VisitFactoryInterface    $factory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    public function register(User $user, Event $event, array $data = []): Visit
    {
        $existingVisit = $this->visitRepository->findExistingVisit($user, $event);

        if ($existingVisit) {
            throw new VisitExistException();
        }

        $visit = $this->factory->createVisit($user, $event, $data);
        $this->visitRepository->save($visit, true);

        $this->eventDispatcher->dispatch(new RegistrationEvent($user, $event));

        return $visit;
    }
}
