<?php

declare(strict_types = 1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class OrdersGroupRequestDto
{
    #[Assert\Positive(message: 'Page must be positive integer')]
    private int $page;

    #[Assert\Positive(message: 'PerPage must be positive integer')]
    #[Assert\Range(
        notInRangeMessage: 'PerPage must be between {{ min }} and {{ max }}',
        min: 1,
        max: 100
    )]
    private int $perPage;

    #[Assert\Choice(
        choices: ['day', 'month', 'year'],
        message: 'GroupBy must be one of: day, month, year'
    )]
    private string $groupBy;

    public function __construct(
        ?int $page = null,
        ?int $perPage = null,
        ?string $groupBy = null
    ) {
        $this->page = $page ?? 1;
        $this->perPage = $perPage ?? 20;
        $this->groupBy = $groupBy ?? 'month';
    }

    public static function fromRequest(array $queryParams): self
    {
        return new self(
            isset($queryParams['page']) ? (int) $queryParams['page'] : null,
            isset($queryParams['perPage']) ? (int) $queryParams['perPage'] : null,
            $queryParams['groupBy'] ?? null
        );
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getGroupBy(): string
    {
        return $this->groupBy;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
