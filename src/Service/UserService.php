<?php

namespace App\Service;

use App\Repository\UserRepository;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}
}
