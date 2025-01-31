<?php

namespace App\DTO;

class TokenDTO
{
    public function __construct(
        public string $header,
        public string $payload,
        public string $signature,
        public object $decodedPayload,
    ) {}
}
