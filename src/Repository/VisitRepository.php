<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Visit;
use App\Trait\SaveableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Visit>
 */
class VisitRepository extends ServiceEntityRepository
{
    use SaveableTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function findExistingVisit(User $user, Event $event): ?Visit
    {
        return $this->findOneBy(['visitor' => $user, 'event' => $event]);
    }
}
