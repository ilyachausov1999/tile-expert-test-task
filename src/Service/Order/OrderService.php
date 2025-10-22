<?php

declare(strict_types = 1);

namespace App\Service\Order;

use App\Dto\CreateOrderRequestDto;
use App\Dto\CreateOrderResponseDto;
use App\Dto\OrdersGroupRequestDto;
use App\Dto\OrdersGroupResponseDto;
use App\Dto\OrdersGroupItemDto;
use App\Repository\OrderArticlesRepository;
use App\Repository\OrderDeliveryRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SoapFault;
use Symfony\Component\Serializer\SerializerInterface;

readonly class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderDeliveryRepository $deliveryRepository,
        private OrderArticlesRepository $articlesRepository,
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @param int $orderId
     * @return array|null
     */
    public function getOrderDetail(int $orderId): ?array
    {
        $order = $this->orderRepository->getOrderDetail($orderId);

        if (!$order) {
            return null;
        }

        return $this->serializer->normalize($order, null, [
            'groups' => ['order_detail', 'order_articles', 'order_delivery'],
            'datetime_format' => 'Y-m-d H:i:s',
            'date_format' => 'Y-m-d'
        ]);
    }

    /**
     * @param OrdersGroupRequestDto $groupRequestDto
     * @return OrdersGroupResponseDto
     */
    public function getGroupedOrders(OrdersGroupRequestDto $groupRequestDto): OrdersGroupResponseDto
    {
        $groupByExpression = $this->getGroupByExpression($groupRequestDto->getGroupBy());

        $totalOrders = $this->orderRepository->getTotalOrdersCount();
        $totalGroups = $this->orderRepository->getTotalGroupsCount($groupByExpression);
        $groupedData = $this->orderRepository->getGroupedData($groupRequestDto, $groupByExpression);
        $items = array_map(
            fn($item) => new OrdersGroupItemDto($item['period'], (int) $item['count']),
            $groupedData
        );

        return new OrdersGroupResponseDto(
            $groupRequestDto->getPage(),
            $groupRequestDto->getPerPage(),
            $totalGroups,    // totalPages рассчитывается от количества групп
            $totalOrders,    // общее количество заказов
            $groupRequestDto->getGroupBy(),
            $items
        );
    }

    /**
     * @param string $groupBy
     * @return string
     */
    private function getGroupByExpression(string $groupBy): string
    {
        return match ($groupBy) {
            'day' => "DATE_FORMAT(o.createdAt, '%Y-%m-%d')",
            'month' => "DATE_FORMAT(o.createdAt, '%Y-%m')",
            'year' => "DATE_FORMAT(o.createdAt, '%Y')",
        };
    }

    /**
     * @throws SoapFault
     * @throws \Doctrine\DBAL\Exception
     */
    public function createOrder(CreateOrderRequestDto $orderDto): CreateOrderResponseDto
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $order = $this->orderRepository->createOrderEntity($orderDto);

            $this->articlesRepository->createOrderArticles($order, $orderDto->articles);

            if ($orderDto->delivery) {
                $this->deliveryRepository->createOrderDelivery($order, $orderDto->delivery);
            }

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            return new CreateOrderResponseDto(
                success: true,
                orderId: $order->getId(),
                orderNumber: $order->getNumber(),
                hash: $order->getHash(),
                token: $order->getToken(),
                message: 'Order created successfully'
            );
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw new SoapFault('SERVER', 'Failed to create order: ' . $e->getMessage());
        }
    }
}
