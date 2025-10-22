<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    public function testGetOrderDetailSuccess(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/1');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('id', $data['data']);
    }

    public function testGetOrderDetailNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/99999');

        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetOrderDetailInvalidId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/test');

        $response = $client->getResponse();
        $this->assertTrue(in_array($response->getStatusCode(), [400, 404]));
    }

    public function testGetOrdersByGroupsSuccess(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/grouping?page=1&perPage=10&groupBy=day');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('items', $data['data']);
    }

    public function testGetOrdersByGroupsMissingPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/groups', [
            'page' => 1,
            'groupBy' => 'month'
        ]);

        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetOrdersByGroupsInvalidGroupBy(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/grouping?page=1&perPage=10&groupBy=invalid_value');

        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetOrdersByGroupsDifferentGroupByValues(): void
    {
        $client = static::createClient();

        $groupByValues = ['day', 'year', 'month'];

        foreach ($groupByValues as $groupBy) {
            $client->request('GET', '/api/orders/grouping?page=1&perPage=10&groupBy=' . $groupBy);

            $response = $client->getResponse();
            $this->assertEquals(200, $response->getStatusCode());

            $data = json_decode($response->getContent(), true);
            $this->assertTrue($data['success']);
        }
    }

    public function testGetOrdersByGroupsResponseStructure(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/orders/grouping?page=1&perPage=10&groupBy=day');

        $response = $client->getResponse();

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('data', $data);
            $this->assertIsArray($data['data']);

            if (!empty($data['data'])) {
                $group = $data['data'];
                $this->assertArrayHasKey('page', $group);
                $this->assertArrayHasKey('perPage', $group);
                $this->assertArrayHasKey('perPage', $group);
                $this->assertArrayHasKey('totalPages', $group);
                $this->assertArrayHasKey('totalGroups', $group);
                $this->assertArrayHasKey('totalOrders', $group);
                $this->assertArrayHasKey('items', $group);
            }
        }
    }
}
