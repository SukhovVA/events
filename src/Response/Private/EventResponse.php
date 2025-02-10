<?php

namespace App\Response\Private;

use App\Entity\Event;
use JsonSerializable;

class EventResponse implements JsonSerializable
{
    public function __construct(private readonly Event $event) {}

    public function jsonSerialize(): array
    {
        return [
            'id'            => $this->event->getId(),
            'name'          => $this->event->getName(),
            'description'   => $this->event->getDescription(),
            'starts_at'     => $this->event->getStartsAt(),
            "ends_at"       => $this->event->getEndsAt(),
            'academicHours' => $this->event->getAcademicHours(),
            'remoteLink'    => $this->event->getRemoteLink(),
            'slug'          => $this->event->getSlug(),
            'active'        => $this->event->isActive(),
            'cover'         => new MediaLinkResponse($this->event->getCover()),
            'visits'        => $this->event->getVisits()->count(),
            'properties'    => $this->event->getProperties()->map(fn($property) => new PropertyResponse($property)),
            'created_at'    => $this->event->getCreatedAt(),
            'updated_at'    => $this->event->getUpdatedAt(),
            'deleted_at'    => $this->event->getDeletedAt(),
            'deleted'       => $this->event->isDeleted(),
        ];
    }
}
