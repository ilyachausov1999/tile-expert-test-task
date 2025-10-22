<?php

declare(strict_types=1);

namespace App\Dto;

use stdClass;

class CreateOrderRequestDto
{
    public function __construct(
        public int $managerId,
        public int $statusId,
        public string $name,
        public int $payType,
        public string $locale,
        public string $currency,
        public string $measure,
        public array $articles = [],
        public ?string $description = null,
        public ?int $userId = null,
        public ?DeliveryDataDto $delivery = null
    ) {}

    public static function fromSoapRequest(stdClass $data): self
    {
        $articles = [];
        if (isset($data->articles->article)) {
            $articlesData = is_array($data->articles->article) ? $data->articles->article : [$data->articles->article];

            foreach ($articlesData as $articleData) {
                $articles[] = new ArticleDataDto(
                    articleId: (int) $articleData->articleId,
                    amount: (string) $articleData->amount,
                    price: (string) $articleData->price,
                    displayMeasure: $articleData->displayMeasure ?? null,
                    specialNotes: $articleData->specialNotes ?? null
                );
            }
        }

        $delivery = null;
        if (isset($data->delivery)) {
            $delivery = new DeliveryDataDto(
                countryId: isset($data->delivery->countryId) ? (int) $data->delivery->countryId : null,
                regionId: isset($data->delivery->regionId) ? (int) $data->delivery->regionId : null,
                cityId: isset($data->delivery->cityId) ? (int) $data->delivery->cityId : null,
                amount: isset($data->delivery->amount) ? (string) $data->delivery->amount : null,
                typeId: (int) $data->delivery->typeId,
                fullAddress: $data->delivery->fullAddress,
                address: $data->delivery->address ?? null,
                postalCode: $data->delivery->postalCode ?? null
            );
        }

        return new self(
            managerId: (int) $data->managerId,
            statusId: (int) $data->statusId,
            name: $data->name,
            payType: (int) $data->payType,
            locale: $data->locale,
            currency: $data->currency,
            measure: $data->measure,
            articles: $articles,
            description: $data->description ?? null,
            userId: isset($data->userId) ? (int) $data->userId : null,
            delivery: $delivery
        );
    }
}
