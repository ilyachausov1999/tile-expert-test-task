<?php

declare(strict_types = 1);

namespace App\Service\Order;

use App\Dto\OrdersGroupRequestDto;
use App\Dto\OrdersGroupResponseDto;

interface OrderServiceInterface
{
    /**
     * @param OrdersGroupRequestDto $groupRequestDto
     * @return OrdersGroupResponseDto
     */
    public function getGroupedOrders(OrdersGroupRequestDto $groupRequestDto): OrdersGroupResponseDto;
}
