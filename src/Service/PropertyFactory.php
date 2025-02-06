<?php

namespace App\Service;


use App\Entity\Property;

class PropertyFactory
{
    public function create(string $className, string $uuid, string $name): Property
    {
        return (new $className())
            ->setUuid($uuid)
            ->setName($name);
    }
}
