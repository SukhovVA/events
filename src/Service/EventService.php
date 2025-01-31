<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Visit;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class EventService
{
    public function __construct(
        private EventRepository        $eventRepository,
        private TagAwareCacheInterface $cache,
        private EntityManagerInterface $em
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

    public function getActiveEvent(string $slug): ?Event
    {
        return $this->eventRepository->findOneBy(['slug' => $slug, 'active' => true]);
    }

    public function register(Event $event, ?UserInterface $user): void
    {
        $visit = (new Visit())
            ->setEvent($event)
            ->setVisitor($user)
            ->setVisited(false)
        ;

        $this->em->persist($visit);
        $this->em->flush();
    }
}
