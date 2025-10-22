<?php

declare(strict_types = 1);

namespace App\Service\Order;

use App\Dto\CreateOrderRequestDto;
use App\Dto\CreateOrderResponseDto;
use App\Dto\OrdersGroupRequestDto;
use App\Dto\OrdersGroupResponseDto;

interface OrderServiceInterface
{
    /**
     * @param int $orderId
     * @return array|null
     */
    public function getOrderDetail(int $orderId): ?array;

    /**
     * @param OrdersGroupRequestDto $groupRequestDto
     * @return OrdersGroupResponseDto
     */
    public function getGroupedOrders(OrdersGroupRequestDto $groupRequestDto): OrdersGroupResponseDto;

    /**
     * @param CreateOrderRequestDto $orderDto
     * @return CreateOrderResponseDto
     */
    public function createOrder(CreateOrderRequestDto $orderDto): CreateOrderResponseDto;
}
