<?php

namespace App\Command;

use App\DTO\ProsvPropertyResponse;
use App\Entity\Property\Grade;
use App\Entity\Property\StudyLevel;
use App\Entity\Property\Subject;
use App\Entity\Property\Umk;
use App\Enum\ProsvAttribute;
use App\Gateway\ProsvGateway;
use App\Service\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-props',
    description: 'Update properties',
)]
class UpdatePropertiesCommand extends Command
{
    public function __construct(
        private readonly ProsvGateway           $prosvGateway,
        private readonly PropertyService        $propertyProvider,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $mappings = [
            [
                'attribute' => ProsvAttribute::Subject,
                'class'     => Subject::class,
            ],
            [
                'attribute' => ProsvAttribute::Series,
                'class'     => Umk::class,
            ],
            [
                'attribute' => ProsvAttribute::Grade,
                'class'     => Grade::class,
            ],
            [
                'attribute' => ProsvAttribute::Level,
                'class'     => StudyLevel::class,
            ],
        ];

        $results = [];

        foreach ($mappings as $attribute) {
            /** @var ProsvPropertyResponse[] $dtos */
            $dtos = $this->prosvGateway->getAttributes($attribute['attribute']);
            $count = 0;
            foreach ($dtos as $dto) {
                $this->propertyProvider->createOrUpdate(
                    $attribute['class'],
                    $dto->uuid,
                    $dto->name,
                );
                $count++;
            }
            $results[$attribute['class']] = $count;
        }

        $this->entityManager->flush();

        $messages = [];
        foreach ($results as $label => $count) {
            $messages[] = "$label: $count";
        }
        $io->success('Обновлено/создано: ' . implode(', ', $messages));

        return Command::SUCCESS;
    }
}
