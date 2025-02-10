<?php

namespace App\Response\Private;

use App\Entity\MediaLink;
use JsonSerializable;

class MediaLinkResponse implements JsonSerializable
{
    public function __construct(private readonly MediaLink $mediaLink) {}

    public function jsonSerialize(): array
    {
        return [
            'id'           => $this->mediaLink->getId(),
            'name'         => $this->mediaLink->getName(),
            'originalName' => $this->mediaLink->getOriginalName(),
            'hash'         => $this->mediaLink->getHash(),
            'type'         => $this->mediaLink->getType(),
            'path'         => $this->mediaLink->getPath(),
            'createdAt'    => $this->mediaLink->getCreatedAt(),
            'updatedAt'    => $this->mediaLink->getUpdatedAt(),
        ];
    }
}
