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
            'slug'           => $this->event->getSlug(),
            'name'           => $this->event->getName(),
            'cover'          => $this->event->getCover()->getName(),
            'description'    => $this->event->getDescription(),
            'academic_hours' => $this->event->getAcademicHours(),
            'starts_at'      => $this->event->getStartsAt(),
            'ends_at'        => $this->event->getEndsAt(),
        ];
    }
}
