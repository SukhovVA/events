<?php

namespace App\Service\Private;

use App\Entity\Event;
use DateTimeImmutable;

class CommonEventFactory implements EventFactoryInterface
{
    public function create(string $name, string $description, DateTimeImmutable $startsAt, DateTimeImmutable $endsAt, bool $active, ?float $academicHours): Event
    {
        return (new Event())
            ->setName($name)
            ->setDescription($description)
            ->setStartsAt($startsAt)
            ->setEndsAt($endsAt)
            ->setAcademicHours($academicHours)
            ->setActive($active)
        ;
    }
}
