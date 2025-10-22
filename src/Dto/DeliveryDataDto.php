<?php

declare(strict_types = 1);

namespace App\Dto;

class DeliveryDataDto
{
    public function __construct(
        public ?int $countryId = null,
        public ?int $regionId = null,
        public ?int $cityId = null,
        public ?string $amount = null,
        public int $typeId = 0,
        public string $fullAddress = '',
        public ?string $address = null,
        public ?string $postalCode = null
    ) {}
}
