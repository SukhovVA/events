<?php

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class EventService
{
    public function __construct(private EventRepository $eventRepository) {}

    public function getEvents(?UserInterface $user, int $page): array
    {
        return $this->eventRepository->findLatest($user, $page);
    }

    public function getActiveEvent(string $slug): ?Event
    {
        return $this->eventRepository->findOneBy(['slug' => $slug, 'active' => true]);
    }
}
