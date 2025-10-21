<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        echo "debug!\n";
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
            RegionFixtures::class,
            CityFixtures::class,
            OrderStatusFixtures::class,
            ManagerFixtures::class,
            UserFixtures::class,
            ArticleFixtures::class,
            OrderFixtures::class,
            OrderDeliveryFixtures::class,
            OrderArticleFixtures::class,
        ];
    }
}
