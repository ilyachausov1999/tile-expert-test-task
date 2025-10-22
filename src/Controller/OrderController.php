<?php

namespace App\Controller;

use App\Dto\OrdersGroupResponseDto;
use App\Entity\Order;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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

#[OA\Tag(name: "Orders")]
final class OrderController extends AbstractController
{
    use ApiResponseTrait;
    use ValidationTrait;

    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {}

    #[Route('/api/orders/grouping', name: 'api_orders_grouping', methods: ['GET'])]
    #[OA\Get(
        path: "/api/orders/grouping",
        description: "Retrieve detailed information about specific order",
        summary: "Get orders by groups"
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    #[OA\Parameter(
        name: 'perPage',
        description: 'Items per page',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 10)
    )]
    #[OA\Parameter(
        name: 'groupBy',
        description: 'Group by period',
        in: 'query',
        schema: new OA\Schema(type: 'string', enum: ['day', 'month', 'year'])
    )]
    #[OA\Response(
        response: 200,
        description: 'Grouped orders data',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: OrdersGroupResponseDto::class))
        )
    )]    public function getOrdersByGroups(
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

    #[Route('/api/orders/{id}', name: 'api_order_detail', methods: ['GET'])]
    #[OA\Get(
        path: "/api/orders/{id}",
        description: "Retrieve detailed information about specific order",
        summary: "Get order details"
    )]
    #[OA\Parameter(
        name: "id",
        description: "Order ID",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Order details",
        content: new OA\JsonContent(ref: new Model(type: Order::class))
    )]
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
}
