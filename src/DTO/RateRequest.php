<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 10)]
        public int $rating,
    ) {}
}
