<?php

namespace App\Service\Private;

use App\Entity\MediaLink;

class CommonMediaLinkFactory implements MediaLinkFactoryInterface
{

    public function create(string $name, string $originalName, string $hash, int $type): MediaLink
    {
       return (new MediaLink())
            ->setOriginalName($name)
            ->setType($type)
            ->setName($originalName)
            ->setHash($hash)
        ;
    }
}
