<?php

namespace App\Controller;

use App\Dto\OrdersGroupRequestDto;
use App\Service\Order\OrderServiceInterface;
use App\Trait\ApiResponseTrait;
use App\Trait\ValidationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class OrderController extends AbstractController
{
    use ApiResponseTrait;
    use ValidationTrait;

    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {}

    #[Route('/api/orders/{id}', name: 'api_order_detail', methods: ['GET'])]
    public function getOrderDetail(int $id): JsonResponse
    {
        try {
            $orderData = $this->orderService->getOrderDetail($id);

            if (!$orderData) {
                return $this->jsonError('Order not found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->jsonSuccess($orderData);
        } catch (\Exception $e) {
            return $this->jsonError('Failed to retrieve order details', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/orders/grouping', name: 'api_orders_grouping', methods: ['GET'])]
    public function getOrdersByGroups(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $groupRequestDto = OrdersGroupRequestDto::fromRequest($request->query->all());
        $errors = $validator->validate($groupRequestDto);

        if (count($errors) > 0) {
            return $this->jsonError('Validation failed', $this->formatValidationErrors($errors));
        }

        try {
            $response = $this->orderService->getGroupedOrders($groupRequestDto);
            return $this->jsonSuccess($response->toArray());
        } catch (\Exception $e) {
            return $this->jsonError('Failed to retrieve statistics', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
