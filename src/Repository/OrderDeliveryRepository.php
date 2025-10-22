<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderDeliveryRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDelivery::class);
    }

    /**
     * @param Order $order
     * @param $deliveryData
     * @return OrderDelivery
     */
    public function createOrderDelivery(Order $order, $deliveryData): OrderDelivery
    {
        var_dump($order->getId());

        $orderDelivery = new OrderDelivery();
        $orderDelivery->setOrder($order);
        $orderDelivery->setCountryId($deliveryData->countryId);
        $orderDelivery->setRegionId($deliveryData->regionId);
        $orderDelivery->setCityId($deliveryData->cityId);
        $orderDelivery->setAmount($deliveryData->amount);
        $orderDelivery->setTypeId((bool)$deliveryData->typeId);
        $orderDelivery->setFullAddress($deliveryData->fullAddress);
        $orderDelivery->setAddress($deliveryData->address);
        $orderDelivery->setPostalCode($deliveryData->postalCode);

        $this->getEntityManager()->persist($orderDelivery);

        return $orderDelivery;
    }
}
