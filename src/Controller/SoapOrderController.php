<?php

namespace App\Controller;

use App\Dto\CreateOrderRequestDto;
use App\Service\Order\OrderServiceInterface;
use Exception;
use RuntimeException;
use SoapFault;
use SoapServer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SoapOrderController extends AbstractController
{
    public function __construct(
        private readonly OrderServiceInterface $orderService,
        private readonly ParameterBagInterface $parameterBag
    ) {}

    #[Route('/soap/order', name: 'soap_order')]
    public function soapEndpoint(Request $request): Response
    {
        $wsdlPath = $this->parameterBag->get('kernel.project_dir') . '/config/soap/order.wsdl.xml';

        if (!file_exists($wsdlPath)) {
            throw new RuntimeException('WSDL file not found: ' . $wsdlPath);
        }

        if ($request->query->get('wsdl')) {
            return new Response(
                file_get_contents($wsdlPath),
                200,
                ['Content-Type' => 'application/xml']
            );
        }

        $soapServer = new SoapServer($wsdlPath, [
            'uri' => 'http://localhost/soap/order',
            'soap_version' => SOAP_1_2
        ]);

        $soapServer->setObject($this);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/soap+xml; charset=utf-8');

        ob_start();
        $soapServer->handle($request->getContent());
        $response->setContent(ob_get_clean());

        return $response;
    }

    /**
     * @param $request
     * @return array
     * @throws SoapFault
     */
    public function createOrder($request): array
    {
        try {
            if (is_array($request)) {
                $request = (object) $request;
            }

            if (empty($request->managerId) || empty($request->statusId) || empty($request->name)) {
                throw new SoapFault('CLIENT', 'Required fields (managerId, statusId, name) are missing');
            }

            $createOrderDto = CreateOrderRequestDto::fromSoapRequest($request);
            $result = $this->orderService->createOrder($createOrderDto);

            return $result->toArray();
        } catch (SoapFault $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SoapFault('SERVER', 'Internal server error: ' . $e->getMessage());
        }
    }
}
