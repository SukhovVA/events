<?php

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class EventService
{
    public function __construct(
        private EventRepository        $eventRepository,
        private TagAwareCacheInterface $cache,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function getEvents(?UserInterface $user, int $page): array
    {
        //TODO: обработать передачу прользователя
        return $this->cache->get("events_$page", function (ItemInterface $item) use ($user, $page) {
            $item->expiresAfter(10800)->tag('events');

            return $this->eventRepository->findLatest($user, $page);
        });
    }

    /**
     * @param string $id
     * @return Event|null
     */
    public function getActiveEventOrFail(string $id): ?Event
    {
        $event = $this->eventRepository->findOneBy([
            'id'     => $id,
            'active' => true
        ]);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        return $event;
    }
}
