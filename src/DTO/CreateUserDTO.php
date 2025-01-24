<?php

namespace App\DTO;

class CreateUserDTO
{
    public function __construct(
        public string $uuid,
        public string $firstName,
        public string $lastName,
        public string $fatherName,
        public string $email,
    ) {}
}
