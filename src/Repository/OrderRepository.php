<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Dto\CreateOrderRequestDto;
use App\Dto\OrdersGroupRequestDto;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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
     * @param int $orderId
     * @return Order|null
     */
    public function getOrderDetail(int $orderId): ?Order
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.orderArticles', 'articles')
            ->leftJoin('o.delivery', 'delivery')
            ->addSelect('articles')
            ->addSelect('delivery')
            ->where('o.id = :id')
            ->setParameter('id', $orderId)
            ->getQuery()
            ->getOneOrNullResult();
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

    /**
     * @param CreateOrderRequestDto $orderDto
     * @return Order
     * @throws Exception
     */
    public function createOrderEntity(CreateOrderRequestDto $orderDto): Order
    {
        $order = new Order();
        $order->setHash(md5('testtts' . '_' . time()));
        $order->setUserId($orderDto->userId);
        $order->setManagerId($orderDto->managerId);
        $order->setStatusId($orderDto->statusId);
        $order->setToken(bin2hex(random_bytes(32)));
        $order->setNumber("test" . '_' . time());
        $order->setName($orderDto->name);
        $order->setDescription($orderDto->description);
        $order->setPayType($orderDto->payType);
        $order->setLocale($orderDto->locale);
        $order->setCurrency($orderDto->currency);
        $order->setMeasure($orderDto->measure);
        $order->setStep(1);

        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();

        return $order;
    }
}
