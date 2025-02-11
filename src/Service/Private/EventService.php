<?php

namespace App\Service\Private;

use App\DTO\EventRequest;
use App\Entity\Event;
use App\Repository\Private\EventRepository;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventService
{
    public function __construct(
        private EventRepository       $eventRepository,
        private EventFactoryInterface $eventFactory,
    ) {}

    public function getEvents(int $page): array
    {
        return $this->eventRepository->findLatest(null, $page);
    }

    /**
     * @param string $id
     * @return Event|null
     */
    public function getEventOrFail(string $id): ?Event
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        return $event;
    }

    public function create(EventRequest $request): Event
    {
        $event = $this->eventFactory->create(
            $request->name,
            $request->description,
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->startsAt),
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->endsAt),
            $request->active,
            $request->academicHours
        );

        $this->eventRepository->save($event);

        return $event;
    }

    public function update(string $id, EventRequest $request): Event
    {
        $event = $this->getEventOrFail($id);

        $event
            ->setName($request->name)
            ->setDescription($request->description)
            ->setStartsAt($request->startsAt)
            ->setEndsAt($request->endsAt)
            ->setAcademicHours($request->academicHours)
            ->setActive($request->active)
        ;

        $this->eventRepository->save($event);

        return $event;
    }

    public function delete(string $id): void
    {
        $event = $this->getEventOrFail($id);

        $this->eventRepository->remove($event);
    }
}
