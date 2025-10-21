<?php

declare(strict_types = 1);

namespace App\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    private function jsonSuccess(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $data,
            'timestamp' => time()
        ], $status);
    }

    private function jsonError(string $message, array $details = [], int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'error' => $message,
            'details' => $details,
            'timestamp' => time()
        ], $status);
    }
}
