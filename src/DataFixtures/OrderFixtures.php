<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Manager;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $order = new Order();
            $order->setHash(bin2hex(random_bytes(16)));
            $order->setUserId($this->getReference('user_' . $faker->numberBetween(0, 19), User::class)->getId());
            $order->setManagerId($this->getReference('manager_admin',  Manager::class)->getId());
            $order->setStatusId($this->getReference('status_' . $faker->randomElement(['draft', 'pending', 'confirmed', 'processing']), OrderStatus::class)->getId());
            $order->setToken(bin2hex(random_bytes(32)));
            $order->setNumber($faker->boolean(80) ? 'ORD-' . str_pad((string)($i + 1), 6, '0', STR_PAD_LEFT) : null);
            $order->setName('Order ' . ($i + 1) . ' - ' . $faker->words(2, true));
            $order->setDescription($faker->boolean(50) ? $faker->sentence() : null);
            $order->setPayType($faker->numberBetween(1, 3));
            $order->setDiscount('20');
            $order->setCurRate((string)$faker->randomFloat(6, 0.8, 1.2));
            $order->setSpecPrice($faker->boolean(20));
            $order->setLocale($faker->randomElement(['en', 'de', 'fr', 'it']));
            $order->setCurrency($faker->randomElement(['EUR', 'USD', 'GBP']));
            $order->setMeasure($faker->randomElement(['m', 'cm', 'mm']));
            $order->setWeightGross((string)$faker->randomFloat(3, 10, 500));
            $order->setStep($faker->numberBetween(1, 3));
            $order->setAddressEqual($faker->boolean(70));
            $order->setBankTransferRequested($faker->boolean(40));
            $order->setAcceptPay($faker->boolean(60));
            $order->setAcceptPay($faker->boolean(60));
            $order->setProductReview($faker->boolean(30));
            $order->setProcess($faker->boolean(20));
            $order->setProcess($faker->boolean(20));
            $order->setShowMsg($faker->boolean(10));

            $randomDate = $faker->dateTimeBetween('-2 years', 'now');
            $order->setCreatedAt($randomDate);

            $manager->persist($order);
            $this->addReference('order_' . $i, $order);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ManagerFixtures::class,
            OrderStatusFixtures::class,
        ];
    }
}
