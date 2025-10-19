<?php

declare(strict_types = 1);

namespace App\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class ApiHelper implements ApiRequestInterface
{
    private Client $client;

    private string $baseUrl;

    /**
     * @param string $baseApiUrl
     */
    public function __construct(
        string $baseApiUrl
    ) {
        $this->baseUrl = $baseApiUrl;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; Symfony Price Parser)',
            ]
        ]);
    }

    /**
     * @param string $url
     * @param string $method
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function sendApiRequest(string $url = '', string $method = 'GET'): ResponseInterface
    {
        return $this->client->request($method, $url);
    }
}
