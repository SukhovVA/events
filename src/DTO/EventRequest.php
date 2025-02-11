<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class EventRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 255)]
        public string            $name,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 3000)]
        public string            $description,

        #[Assert\NotBlank]
        #[Assert\DateTime]
        public string            $startsAt,

        #[Assert\NotBlank]
        #[Assert\DateTime]
        public string            $endsAt,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Url(requireTld: false)]
        public string            $remoteLink,

        #[Assert\Type('float')]
        public ?float            $academicHours = null,

        #[Assert\Type('boolean')]
        public ?bool             $active = true,
    ) {}
}
