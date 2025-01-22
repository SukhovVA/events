<?php

namespace App\Factory;

use App\Entity\MediaLink;
use App\Enum\MediaLinkType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<MediaLink>
 */
final class MediaLinkFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return MediaLink::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     */
    protected function defaults(): array|callable
    {
        return [
            'name'         => self::faker()->uuid() . '.jpg',
            'originalName' => 'cover.jpg',
            'hash'         => self::faker()->sha256(),
            'type'         => MediaLinkType::COVER->value,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(MediaLink $mediaLink): void {})
        ;
    }
}
