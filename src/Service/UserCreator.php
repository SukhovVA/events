<?php

namespace App\Service;

use App\DTO\CreateUserResponse;
use App\Entity\User;
use App\Repository\UserRepository;

readonly class UserCreator
{
    public function __construct(private UserRepository $userRepository) {}

    public function createUser(CreateUserResponse $userData): User
    {
        $user = new User();
        $user->setUuid($userData->uuid);
        $user->setFirstName($userData->firstName);
        $user->setLastName($userData->lastName);
        $user->setFatherName($userData->fatherName);
        $user->setEmail($userData->email);
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user, true);

        return $user;
    }
}
