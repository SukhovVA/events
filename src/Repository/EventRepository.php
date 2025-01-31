<?php

namespace App\Repository;

use App\Entity\Event;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findLatest(?UserInterface $user, int $page)
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.active = true')
            ->getQuery();

        return (new Paginator($query))->paginate($page);
    }

}
