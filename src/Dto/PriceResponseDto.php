<?php

declare(strict_types = 1);

namespace App\Dto;

readonly class PriceResponseDto
{
    public function __construct(
        private bool $success = true,
        private ?float $price = null,
        private ?string $factory = null,
        private ?string $collection = null,
        private ?string $article = null,
        private ?string $error = null,
        private array $details = []
    ) {}

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getFactory(): ?string
    {
        return $this->factory;
    }

    public function getCollection(): ?string
    {
        return $this->collection;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function toArray(): array
    {
        return array_filter([
            'success' => $this->success,
            'price' => $this->price,
            'factory' => $this->factory,
            'collection' => $this->collection,
            'article' => $this->article,
            'error' => $this->error,
            'details' => $this->details ?: [],
        ], fn($value) => $value !== null);
    }
}
