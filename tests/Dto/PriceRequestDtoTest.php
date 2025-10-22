<?php

namespace App\Tests\Dto;

use App\Dto\PriceRequestDto;
use PHPUnit\Framework\TestCase;

class PriceRequestDtoTest extends TestCase
{
    public function testFromRequestWithAllParameters(): void
    {
        $queryParams = [
            'factory' => 'Test Factory',
            'collection' => 'Test Collection',
            'article' => 'TEST-123'
        ];

        $dto = PriceRequestDto::fromRequest($queryParams);

        $this->assertEquals('Test Factory', $dto->getFactory());
        $this->assertEquals('Test Collection', $dto->getCollection());
        $this->assertEquals('TEST-123', $dto->getArticle());
    }

    public function testFromRequestWithMissingParameters(): void
    {
        $queryParams = [
            'factory' => 'Test Factory'
        ];

        $dto = PriceRequestDto::fromRequest($queryParams);

        $this->assertEquals('Test Factory', $dto->getFactory());
        $this->assertNull($dto->getCollection());
        $this->assertNull($dto->getArticle());
    }

    public function testFromRequestWithEmptyParameters(): void
    {
        $queryParams = [
            'factory' => '',
            'collection' => '',
            'article' => ''
        ];

        $dto = PriceRequestDto::fromRequest($queryParams);

        $this->assertEquals('', $dto->getFactory());
        $this->assertEquals('', $dto->getCollection());
        $this->assertEquals('', $dto->getArticle());
    }

    public function testFromRequestWithNullParameters(): void
    {
        $queryParams = [
            'factory' => null,
            'collection' => null,
            'article' => null
        ];

        $dto = PriceRequestDto::fromRequest($queryParams);

        $this->assertNull($dto->getFactory());
        $this->assertNull($dto->getCollection());
        $this->assertNull($dto->getArticle());
    }

    public function testFromRequestWithNoParameters(): void
    {
        $queryParams = [];

        $dto = PriceRequestDto::fromRequest($queryParams);

        $this->assertNull($dto->getFactory());
        $this->assertNull($dto->getCollection());
        $this->assertNull($dto->getArticle());
    }
}
