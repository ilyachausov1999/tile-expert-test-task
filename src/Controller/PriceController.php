<?php

namespace App\Controller;

use App\Dto\PriceResponseDto;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Dto\PriceRequestDto;
use App\Exception\PriceParserException;
use App\Service\PriceParse\PriceParserServiceInterface;
use App\Trait\ApiResponseTrait;
use App\Trait\ValidationTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: "Prices")]
final class PriceController extends AbstractController
{
    use ApiResponseTrait;
    use ValidationTrait;

    public function __construct(
        private readonly PriceParserServiceInterface $priceParser
    ) {}

    #[Route('/api/price', name: 'api_price', methods: ['GET'])]
    #[OA\Get(
        description: 'Retrieve current price for a product from external source',
        summary: 'Get product price'
    )]
    #[OA\Parameter(
        name: 'factory',
        description: 'Product factory/manufacturer',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string', example: 'Porcelanosa')
    )]
    #[OA\Parameter(
        name: 'collection',
        description: 'Product collection name',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string', example: 'Modern Collection')
    )]
    #[OA\Parameter(
        name: 'article',
        description: 'Product article/SKU',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string', example: 'TILE-12345')
    )]
    #[OA\Response(
        response: 200,
        description: 'Price retrieved successfully',
        content: new OA\JsonContent(ref: new Model(type: PriceResponseDto::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation error - missing or invalid parameters'
    )]
    #[OA\Response(
        response: 404,
        description: 'Price not found for the specified product'
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error or external service unavailable'
    )]
    public function getPrice(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $priceRequestDto = PriceRequestDto::fromRequest($request->query->all());
        $errors = $validator->validate($priceRequestDto);

        if (count($errors) > 0) {
            return $this->jsonError('Validation failed', $this->formatValidationErrors($errors));
        }

        // todo можно было бы еще парсить больше данных по артикулам и записывать в БД при необходимости
        // todo или класть в кэш если они не часто обновляются на сайте

        try {
            $priceResponseDto = $this->priceParser->getPrice($priceRequestDto);
            return $this->jsonSuccess($priceResponseDto->toArray());
        } catch (PriceParserException $e) {
            return $this->jsonError(
                'PriceParserException',
                [$e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (Exception $e) {
            return $this->jsonError(
                'PriceParserException',
                ['An unexpected error occurred'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
