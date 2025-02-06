<?php

namespace App\Repository;

use App\Entity\Event;
use App\Service\Paginator;
use App\Trait\SaveableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    use SaveableTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findLatest(?UserInterface $user, int $page): array
    {
        $query = $this->createQueryBuilder('e')
            ->select([
                'partial e.{id, slug, name, startsAt, endsAt, academicHours}',
                'partial m.{id, name, createdAt}'
            ])
            ->leftJoin('e.cover', 'm')
            ->where('e.active = true')
            ->orderBy('e.startsAt', 'DESC')
            ->getQuery();

        return (new Paginator($query))->paginate($page);
    }

}
