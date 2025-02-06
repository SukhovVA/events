<?php

namespace App\Service\Private;

use App\Entity\Event;
use DateTimeImmutable;

interface EventFactoryInterface {
    public function create(
        string $name,
        string $description,
        DateTimeImmutable $startsAt,
        DateTimeImmutable $endsAt,
        bool $active,
        ?float $academicHours
    ): Event;
}
