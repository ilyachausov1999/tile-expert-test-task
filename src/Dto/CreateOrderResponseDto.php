<?php

declare(strict_types = 1);

namespace App\Dto;

class CreateOrderResponseDto
{
    public function __construct(
        public bool $success,
        public ?int $orderId = null,
        public ?string $orderNumber = null,
        public ?string $hash = null,
        public ?string $token = null,
        public ?string $message = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'orderId' => $this->orderId,
            'orderNumber' => $this->orderNumber,
            'hash' => $this->hash,
            'token' => $this->token,
            'message' => $this->message,
        ];
    }
}
