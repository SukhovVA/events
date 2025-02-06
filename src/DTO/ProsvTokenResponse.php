<?php

namespace App\DTO;

readonly class ProsvTokenResponse
{
    public function __construct(
        public string $header,
        public string $payload,
        public string $signature,
        public object $decodedPayload,
    ) {}
}
