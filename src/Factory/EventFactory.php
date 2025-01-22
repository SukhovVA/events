<?php

namespace App\Factory;

use App\Entity\Event;
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
     * @todo add your default values here
     * @throws \DateMalformedStringException
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
            'createdAt'     => DateTimeImmutable::createFromMutable($startsAt->modify('-2 months')),
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
