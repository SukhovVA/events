<?php

namespace App\DTO;

use App\Enum\MediaLinkType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

readonly class MediaLinkRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\File(maxSize: '10M', mimeTypes: ['image/png', 'image/jpeg', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf', 'application/zip'])]
        public UploadedFile $file,

        #[Assert\Type('integer')]
        public ?int $type = MediaLinkType::COVER->value,
    ) {}
}
