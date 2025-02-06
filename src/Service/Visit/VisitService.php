<?php

namespace App\Service\Visit;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Visit;
use App\Event\RegistrationEvent;
use App\Exception\VisitExistException;
use App\Repository\VisitRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class VisitService
{
    public function __construct(
        private VisitRepository          $visitRepository,
        private VisitFactoryInterface    $factory,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * @throws VisitExistException
     */
    public function register(User $user, Event $event, array $data = []): Visit
    {
        $existingVisit = $this->visitRepository->findExistingVisit($user, $event);

        if ($existingVisit) {
            throw new VisitExistException();
        }

        $visit = $this->factory->createVisit($user, $event, $data);
        $this->visitRepository->save($visit);

        $this->eventDispatcher->dispatch(new RegistrationEvent($user, $event));

        return $visit;
    }

    /**
     * @throws VisitExistException
     */
    public function rate(User $user, Event $event, int $rating): Visit
    {
        $existingVisit = $this->visitRepository->findExistingVisit($user, $event);

        if (!$existingVisit || !$existingVisit->isVisited()) {
            throw new VisitExistException('Visit not found');
        }

        $existingVisit->setRating($rating);

        $this->visitRepository->save($existingVisit);

        return $existingVisit;
    }
}
