<?php

namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

readonly class Paginator
{
    private const PAGE_SIZE = 12;

    public function __construct(
        private Query $query,
        private int   $pageSize = self::PAGE_SIZE
    ) {}

    /**
     * Пагинатор
     *
     * @param int $page
     * @param bool $hintDistinct
     *
     * @return array
     */
    public function paginate(int $page, bool $hintDistinct = true): array
    {
        $paginator = new ORMPaginator($this->query);
        $paginator->setUseOutputWalkers(false);
        $this->query->setHint(CountWalker::HINT_DISTINCT, $hintDistinct);
        $totalItems = $paginator->count();

        $items = $paginator
            ->getQuery()
            ->setFirstResult($this->pageSize * ($page - 1))
            ->setMaxResults($this->pageSize)
            ->getResult();

        return [
            'data' => array_values($items),
            'meta' => [
                'current_page' => $page,
                'last_page'    => ceil($totalItems / $this->pageSize),
                'per_page'     => $this->pageSize,
                'total'        => $totalItems,
            ]
        ];
    }
}
