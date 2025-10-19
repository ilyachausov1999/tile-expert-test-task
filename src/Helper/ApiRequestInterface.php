<?php

declare(strict_types = 1);

namespace App\Helper;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * @package App\Interfaces
 */
interface ApiRequestInterface
{
    /**
     * @param string $url
     * @param string $method
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function sendApiRequest(string $url, string $method): ResponseInterface;
}
