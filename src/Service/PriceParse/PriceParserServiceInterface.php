<?php

declare(strict_types = 1);

namespace App\Service\PriceParse;

use App\Dto\PriceRequestDto;
use App\Dto\PriceResponseDto;

interface PriceParserServiceInterface
{
    /**
     * @param PriceRequestDto $priceDto
     * @return PriceResponseDto
     */
    public function getPrice(PriceRequestDto $priceDto): PriceResponseDto;
}
