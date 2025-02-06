<?php

namespace App\Factory;

use App\Entity\Event;
use App\Factory\Property\GradeFactory;
use App\Factory\Property\StudyLevelFactory;
use App\Factory\Property\SubjectFactory;
use App\Factory\Property\UmkFactory;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Event>
 */
final class EventFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Event::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     */
    protected function defaults(): array|callable
    {
        $startsAt = self::faker()->dateTimeBetween('now', '+1 month');

        return [
            'name'          => self::faker()->text(80),
            'active'        => self::faker()->boolean(),
            'startsAt'      => DateTimeImmutable::createFromMutable($startsAt),
            'endsAt'        => DateTimeImmutable::createFromMutable($startsAt->modify('+1 hours')),
            'description'   => self::faker()->text(2000),
            'academicHours' => self::faker()->randomFloat(1, 1, 10),
            'remoteLink'    => self::faker()->url(),
            'cover'         => MediaLinkFactory::createOne(),
            'properties'    => [
                ...SubjectFactory::randomRange(1, 2),
                ...GradeFactory::randomRange(1, 2),
                ...StudyLevelFactory::randomRange(1, 2),
                ...UmkFactory::randomRange(1, 3)
            ],
            'createdAt'     => DateTimeImmutable::createFromMutable($startsAt->modify('-2 months')),
            'deletedAt'     => self::faker()->boolean(10) ? DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 week')) : null,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Event $event): void {})
        ;
    }
}
