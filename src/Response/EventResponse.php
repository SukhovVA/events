<?php

namespace App\Response;

use App\Entity\Event;
use JsonSerializable;

readonly class EventResponse implements JsonSerializable
{
    public function __construct(private Event $event) {}

    public function jsonSerialize(): array
    {
        return [
            'name'           => $this->event->getName(),
            'slug'           => $this->event->getSlug(),
            'description'    => $this->event->getDescription(),
            'academic_hours' => $this->event->getAcademicHours(),
            'starts_at'      => $this->event->getStartsAt(),
            'ends_at'        => $this->event->getEndsAt(),
            'cover'          => $this->event->getCover()->getPath(),
        ];
    }
}
