<?php

declare(strict_types = 1);

namespace App\Service\PriceParse;

use App\Dto\PriceRequestDto;
use App\Enum\ParsePriceEnum;
use App\Exception\PriceParserException;
use App\Helper\ApiRequestInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

readonly class PriceParserService implements PriceParserServiceInterface
{
    public function __construct(
        private ApiRequestInterface $apiHelper
    ) {}

    /**
     * @param PriceRequestDto $priceDto
     * @return float|null
     * @throws Exception
     */
    public function getPrice(PriceRequestDto $priceDto): ?float
    {
        $url = $this->buildUrl($priceDto);

        try {
            $response = $this->apiHelper->sendApiRequest($url);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            $priceNode = $crawler->filter(ParsePriceEnum::PRICE_SELECTOR->value);

            if($priceNode->count() > 0 && $price = $priceNode->attr(ParsePriceEnum::PRICE_ATTR->value)) {
                return $this->parsePriceFromText($price);
            }

            return null;
        } catch (GuzzleException $e) {
            throw new PriceParserException(
                sprintf('Can\'t found price by url:%s', $url),
                $e->getCode()
            );
        }
    }

    /**
     * @param PriceRequestDto $priceDto
     * @return string
     */
    private function buildUrl(PriceRequestDto $priceDto): string
    {
        return sprintf(
            ParsePriceEnum::MAIN_ROUTE_PATTERN->value,
            urlencode($priceDto->getFactory()),
            urlencode($priceDto->getCollection()),
            urlencode($priceDto->getArticle())
        );
    }

    /**
     * @param string $priceText
     * @return float
     */
    private function parsePriceFromText(string $priceText): float
    {
        $cleanPrice = preg_replace('/[^\d,.]/', '', $priceText);
        $cleanPrice = str_replace(',', '.', $cleanPrice);

        if (preg_match('/\d+\.?\d*/', $cleanPrice, $matches)) {
            return (float) $matches[0];
        }
        return 0;
    }
}
