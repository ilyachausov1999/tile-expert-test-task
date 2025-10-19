<?php

declare(strict_types = 1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PriceRequestDto
{
    #[Assert\NotBlank(message: 'Factory is required')]
    #[Assert\Length(max: 100, maxMessage: 'Factory cannot be longer than 100 characters')]
    private ?string $factory;

    #[Assert\NotBlank(message: 'Collection is required')]
    #[Assert\Length(max: 100, maxMessage: 'Collection cannot be longer than 100 characters')]
    private ?string $collection;

    #[Assert\NotBlank(message: 'Article is required')]
    #[Assert\Length(max: 50, maxMessage: 'Article cannot be longer than 50 characters')]
    private ?string $article;

    public function __construct(
        ?string $factory = null,
        ?string $collection = null,
        ?string $article = null
    ) {
        $this->factory = $factory;
        $this->collection = $collection;
        $this->article = $article;
    }

    /**
     * @param array $queryParams
     * @return self
     */
    public static function fromRequest(array $queryParams): self
    {
        return new self(
            $queryParams['factory'] ?? null,
            $queryParams['collection'] ?? null,
            $queryParams['article'] ?? null
        );
    }

    /**
     * @return string
     */
    public function getFactory(): string
    {
        return $this->factory;
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getArticle(): string
    {
        return $this->article;
    }
}
