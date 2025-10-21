<?php

namespace App\Controller;

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

final class PriceController extends AbstractController
{
    use ApiResponseTrait;
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
            return $this->jsonError('Validation failed', $this->formatValidationErrors($errors));
        }

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
