<?php

namespace App\Command;

use App\Factory\EventFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-data',
    description: 'Create test data',
)]
class TestDataCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $events = EventFactory::createMany(100);

        $io->success(sprintf('Created %d events', count($events)));

        return Command::SUCCESS;
    }
}
