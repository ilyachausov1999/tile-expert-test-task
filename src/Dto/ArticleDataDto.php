<?php

declare(strict_types = 1);

namespace App\Dto;

class ArticleDataDto
{
    public function __construct(
        public int $articleId,
        public string $amount,
        public string $price,
        public ?string $displayMeasure = null,
        public ?string $specialNotes = null
    ) {}
}
