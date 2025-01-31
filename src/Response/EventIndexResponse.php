<?php

namespace App\Response;

use App\Entity\Event;
use JsonSerializable;

readonly class EventIndexResponse implements JsonSerializable
{
    public function __construct(private array $events) {}

    public function jsonSerialize(): array
    {
        return array_map(function (Event $event) {
            return [
                'id'       => $event->getId(),
                'slug'     => $event->getSlug(),
                'name'     => $event->getName(),
                'startsAt' => $event->getStartsAt(),
                'endsAt'   => $event->getEndsAt(),
            ];
        }, $this->events);
    }
}
