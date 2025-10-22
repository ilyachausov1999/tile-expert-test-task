<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoapOrderControllerTest extends WebTestCase
{
    public function testSoapCreateOrderSuccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/soap/order', [], [], [
            'CONTENT_TYPE' => 'text/xml',
        ], $this->createValidSoapRequest());

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getContent();

        $this->assertStringContainsString('<?xml', $content);
        $this->assertStringContainsString('CreateOrderResponse', $content);
        $this->assertStringContainsString('<success>true</success>', $content);
        $this->assertStringContainsString('<orderId>', $content);
        $this->assertStringContainsString('<orderNumber>', $content);
        $this->assertStringContainsString('<hash>', $content);
        $this->assertStringContainsString('<token>', $content);
        $this->assertStringContainsString('<message>', $content);
    }

    private function createValidSoapRequest(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
    <soap:Body>
        <CreateOrderRequest xmlns="http://tile-expert.com/soap/order">
            <userId>2</userId>
            <managerId>1</managerId>
            <statusId>1</statusId>
            <name>SOAP Order Test</name>
            <description>Test order created via SOAP</description>
            <payType>1</payType>
            <locale>en</locale>
            <currency>EUR</currency>
            <measure>m</measure>
            <articles>
                <article>
                    <articleId>7</articleId>
                    <amount>10.5</amount>
                    <price>25.50</price>
                    <displayMeasure>m2</displayMeasure>
                    <specialNotes>Handle with care</specialNotes>
                </article>
                <article>
                    <articleId>5</articleId>
                    <amount>5</amount>
                    <price>15.75</price>
                    <displayMeasure>pcs</displayMeasure>
                </article>
            </articles>
            <delivery>
                <countryId>1</countryId>
                <regionId>5</regionId>
                <cityId>1</cityId>
                <amount>50.00</amount>
                <typeId>0</typeId>
                <fullAddress>123 Main St, Berlin, Germany</fullAddress>
                <address>123 Main St</address>
                <postalCode>10115</postalCode>
            </delivery>
        </CreateOrderRequest>
    </soap:Body>
</soap:Envelope>';
    }

    public function testSoapWsdlAvailable(): void
    {
        $client = static::createClient();

        $client->request('GET', '/soap/order?wsdl=1');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('definitions', $response->getContent());
        $this->assertStringContainsString('CreateOrderRequest', $response->getContent());
        $this->assertStringContainsString('CreateOrderResponse', $response->getContent());
    }
}
