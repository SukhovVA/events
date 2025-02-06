<?php

namespace App\DTO;

readonly class CreateUserResponse
{
    public function __construct(
        public string $uuid,
        public string $firstName,
        public string $lastName,
        public string $fatherName,
        public string $email,
    ) {}
}
