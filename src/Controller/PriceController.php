<?php

namespace App\Controller;

use App\Dto\PriceRequestDto;
use App\Dto\PriceResponseDto;
use App\Exception\PriceParserException;
use App\Service\PriceParse\PriceParserServiceInterface;
use App\Trait\ValidationTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriceController extends AbstractController
{
    use ValidationTrait;

    public function __construct(
        private readonly PriceParserServiceInterface $priceParser
    ) {}

    #[Route('/api/price', name: 'api_price', methods: ['GET'])]
    public function getPrice(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $priceRequestDto = PriceRequestDto::fromRequest($request->query->all());
        $errors = $validator->validate($priceRequestDto);

        if (count($errors) > 0) {
            $response = new PriceResponseDto(
                success: false,
                error: 'Validation failed',
                details: $this->formatValidationErrors($errors)
            );

            return $this->json($response->toArray(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $price = $this->priceParser->getPrice($priceRequestDto);

            if ($price === null) {
                $response = new PriceResponseDto(
                    success: false,
                    error: 'Price not available',
                    details: ['message' => 'Could not retrieve price from the source']
                );

                return $this->json($response->toArray(), Response::HTTP_SERVICE_UNAVAILABLE);
            }

            $response = new PriceResponseDto(
                price: $price,
                factory: $priceRequestDto->getFactory(),
                collection: $priceRequestDto->getCollection(),
                article: $priceRequestDto->getArticle()
            );

            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (PriceParserException $e) {
            $response = new PriceResponseDto(
                success: false,
                error: 'PriceParserException',
                details: ['message' => $e->getMessage()]
            );

            return $this->json($response->toArray(), Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            $response = new PriceResponseDto(
                success: false,
                error: 'Internal server error',
                details: ['message' => 'An unexpected error occurred']
            );

            return $this->json($response->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
