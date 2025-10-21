<?php

declare(strict_types = 1);

namespace App\Dto;

class OrdersGroupItemDto
{
    public string $period;
    public int $count;

    public function __construct(string $period, int $count)
    {
        $this->period = $period;
        $this->count = $count;
    }

    public function toArray(): array
    {
        return [
            'period' => $this->period,
            'count' => $this->count,
        ];
    }
}
