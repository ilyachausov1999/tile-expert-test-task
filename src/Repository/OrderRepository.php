<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Dto\OrdersGroupRequestDto;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return int
     */
    public function getTotalOrdersCount(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $groupByExpression
     * @return int
     */
    public function getTotalGroupsCount(string $groupByExpression): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(DISTINCT ' . $groupByExpression . ')')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param OrdersGroupRequestDto $groupRequestDto
     * @param string $groupByExpression
     * @return array
     */
    public function getGroupedData(OrdersGroupRequestDto $groupRequestDto, string $groupByExpression): array
    {
        return $this->createQueryBuilder('o')
            ->select([
                $groupByExpression . ' as period',
                'COUNT(o.id) as count'
            ])
            ->groupBy('period')
            ->orderBy('period', 'DESC')
            ->setFirstResult($groupRequestDto->getOffset())
            ->setMaxResults($groupRequestDto->getPerPage())
            ->getQuery()
            ->getResult();
    }
}
