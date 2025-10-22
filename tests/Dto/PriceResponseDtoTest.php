<?php

namespace App\Tests\Dto;

use App\Dto\PriceResponseDto;
use PHPUnit\Framework\TestCase;

class PriceResponseDtoTest extends TestCase
{
    public function testToArrayWithSuccess(): void
    {
        $dto = new PriceResponseDto(
            true,
            1500.50,
            'Test Factory',
            'Test Collection',
            'TEST-123',
            null,
            ['available' => true, 'discount' => 10]
        );

        $result = $dto->toArray();

        $this->assertTrue($result['success']);
        $this->assertEquals(1500.50, $result['price']);
        $this->assertEquals('Test Factory', $result['factory']);
        $this->assertEquals('Test Collection', $result['collection']);
        $this->assertEquals('TEST-123', $result['article']);
        $this->assertNull($result['error']);
        $this->assertEquals(['available' => true, 'discount' => 10], $result['details']);
    }

    public function testToArrayWithPartialData(): void
    {
        $dto = new PriceResponseDto(
            true,
            2000.00,
            'Factory Only'
        );

        $result = $dto->toArray();

        $this->assertTrue($result['success']);
        $this->assertEquals(2000.00, $result['price']);
        $this->assertEquals('Factory Only', $result['factory']);
        $this->assertNull($result['collection']);
        $this->assertNull($result['article']);
        $this->assertNull($result['error']);
        $this->assertEquals([], $result['details']);
    }
}
