<?php

namespace App\Service\Private;

use App\Entity\MediaLink;


interface MediaLinkFactoryInterface {
    public function create(
        string            $name,
        string            $originalName,
        string            $hash,
        int               $type
    ): MediaLink;
}
