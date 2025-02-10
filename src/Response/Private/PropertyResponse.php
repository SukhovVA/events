<?php

namespace App\Response\Private;

use App\Entity\Property;

class PropertyResponse implements \JsonSerializable
{

    public function __construct(private Property $property) {}

    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->property->getId(),
            'uuid' => $this->property->getUuid(),
            'name' => $this->property->getName(),
        ];
    }
}
