<?php

namespace App\Repository;

use App\Entity\MediaLink;
use App\Trait\SaveableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaLink>
 */
class MediaLinkRepository extends ServiceEntityRepository
{
    use SaveableTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaLink::class);
    }
}
