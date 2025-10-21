<?php

declare(strict_types = 1);

namespace App\Dto;

class OrdersGroupResponseDto
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $totalGroups,  // количество групп для пагинации
        public int $totalOrders,  // общее количество заказов
        public string $groupBy,
        public array $items
    ) {}

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalGroups / $this->perPage);
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'perPage' => $this->perPage,
            'totalPages' => $this->getTotalPages(),
            'totalGroups' => $this->totalGroups,
            'totalOrders' => $this->totalOrders,
            'groupBy' => $this->groupBy,
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
        ];
    }
}
