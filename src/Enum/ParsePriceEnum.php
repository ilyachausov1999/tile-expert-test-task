<?php

namespace App\Enum;

enum ParsePriceEnum: string
{
    case MAIN_ROUTE_PATTERN = '/fr/tile/%s/%s/a/%s';
    case PRICE_SELECTOR = 'div.price-per-measure-container span.js-price-tag';
    case PRICE_ATTR = 'data-price-raw';
}
