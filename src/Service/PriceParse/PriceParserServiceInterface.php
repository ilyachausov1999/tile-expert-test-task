<?php

declare(strict_types = 1);

namespace App\Service\PriceParse;

use App\Dto\PriceRequestDto;

interface PriceParserServiceInterface
{
    /**
     * @param PriceRequestDto $priceDto
     * @return float|null
     */
    public function getPrice(PriceRequestDto $priceDto): ?float;
}
