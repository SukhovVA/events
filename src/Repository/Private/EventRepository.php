<?php

namespace App\Repository\Private;

use App\Entity\Event;
use App\Trait\SaveableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends \App\Repository\EventRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);

        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_deleteable');
    }
}
