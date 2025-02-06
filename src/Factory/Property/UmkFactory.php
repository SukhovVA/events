<?php

namespace App\Factory\Property;

use App\Entity\Property\Umk;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Umk>
 */
final class UmkFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Umk::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(255),
            'uuid' => self::faker()->uuid(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
    }
}
