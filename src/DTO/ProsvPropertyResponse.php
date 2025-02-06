<?php

namespace App\DTO;

class ProsvPropertyResponse
{
    public function __construct(
        public string $uuid,
        public string $name,
    ) {}
}
