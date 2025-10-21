<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\OrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            ['code' => 'draft', 'name' => 'Draft', 'sort_order' => 1],
            ['code' => 'pending', 'name' => 'Pending', 'sort_order' => 2],
            ['code' => 'confirmed', 'name' => 'Confirmed', 'sort_order' => 3],
            ['code' => 'processing', 'name' => 'Processing', 'sort_order' => 4],
            ['code' => 'shipped', 'name' => 'Shipped', 'sort_order' => 5],
            ['code' => 'delivered', 'name' => 'Delivered', 'sort_order' => 6],
            ['code' => 'cancelled', 'name' => 'Cancelled', 'sort_order' => 7],
        ];

        foreach ($statuses as $statusData) {
            $status = new OrderStatus();
            $status->setCode($statusData['code']);
            $status->setName($statusData['name']);
            $status->setSortOrder($statusData['sort_order']);
            $status->setIsActive(true);

            $manager->persist($status);
            $this->addReference('status_' . $statusData['code'], $status);
        }

        $manager->flush();
    }
}
