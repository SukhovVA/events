<?php

namespace App\Factory;

use App\Entity\MediaLink;
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
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'hash'         => self::faker()->sha256(),
            'name'         => self::faker()->uuid() . '.jpg',
            'originalName' => 'cover.jpg',
            'type'         => 1,
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
