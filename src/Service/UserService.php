<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function getUser(string $uuid): ?User
    {
        return $this->userRepository->findOneBy(['uuid' => $uuid]);
    }
}
