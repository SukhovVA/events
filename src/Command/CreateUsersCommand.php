<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create_users')]
class CreateUsersCommand extends Command
{

    public function __construct(private UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();

        $user->setUuid('56e8b598-4e2b-44d3-a666-65bcd8ff2c05');
        $user->setEmail('john.doe@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setFatherName('Junior');
        $user->setRoles(['ROLE_ADMIN']);
        $this->userRepository->save($user);

        return Command::SUCCESS;
    }
}
