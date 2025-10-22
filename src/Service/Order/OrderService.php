<?php

declare(strict_types = 1);

namespace App\Service\Order;

use App\Dto\OrdersGroupRequestDto;
use App\Dto\OrdersGroupResponseDto;
use App\Dto\OrdersGroupItemDto;
use App\Repository\OrderRepository;
use Symfony\Component\Serializer\SerializerInterface;

readonly class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepository $orderRepository,
        private SerializerInterface $serializer
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
}
