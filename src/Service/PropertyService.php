<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PropertyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyFactory        $propertyFactory,
    ) {}

    /**
     * Создаёт или обновляет сущность-наследника Property по данным.
     *
     * @template T of Property
     * @param class-string<T> $propertyClass Класс наследника Property (например, Subject::class)
     * @param array{uuid: string, name: string} $data Данные для поиска/создания сущности
     * @return T
     */
    public function createOrUpdate(string $propertyClass, string $uuid, string $name): Property
    {
        $repository = $this->entityManager->getRepository($propertyClass);
        /** @var Property|null $entity */
        $entity = $repository->findOneBy(['uuid' => $uuid]);

        if (!$entity) {
            $entity = $this->propertyFactory->create($propertyClass, $uuid, $name);
        } else {
            $entity->setName($name);
        }

        if (!$this->entityManager->contains($entity)) {
            $this->entityManager->persist($entity);
        }

        return $entity;
    }
}
