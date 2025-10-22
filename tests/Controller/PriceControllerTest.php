<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PriceControllerTest extends WebTestCase
{
    public function testGetPriceSuccess(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/price', [
            'factory' => 'abk',
            'collection' => 'poetry-net',
            'article' => '17562-multi-grey-s000620013'
        ]);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $data);
    }

    public function testGetPriceValidationErrorMissingFactory(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/price', [
            'collection' => 'poetry-net',
            'article' => '17562-multi-grey-s000620013'
        ]);

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $data);
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetPriceValidationErrorMissingCollection(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/price', [
            'factory' => 'abk',
            'article' => '17562-multi-grey-s000620013'
        ]);

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetPriceValidationErrorMissingArticle(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/price', [
            'factory' => 'abk',
            'collection' => 'poetry-net'
        ]);

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetPriceValidationErrorEmptyParameters(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/price', [
            'factory' => '',
            'collection' => '',
            'article' => ''
        ]);

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
}
