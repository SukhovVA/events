<?php

namespace App\Service;

use App\DTO\CreateUserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserCreator
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createUser(CreateUserDTO $userData): User
    {
        $user = new User();
        $user->setUuid($userData->uuid);
        $user->setFirstName($userData->firstName);
        $user->setLastName($userData->lastName);
        $user->setFatherName($userData->fatherName);
        $user->setEmail($userData->email);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
