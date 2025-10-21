<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            ['region' => 'BY', 'name' => 'Munich'],
            ['region' => 'BY', 'name' => 'Nuremberg'],
            ['region' => 'NW', 'name' => 'Cologne'],
            ['region' => 'NW', 'name' => 'Dusseldorf'],
            ['region' => 'IDF', 'name' => 'Paris'],
            ['region' => 'PAC', 'name' => 'Marseille'],
            ['region' => 'LOM', 'name' => 'Milan'],
            ['region' => 'TUS', 'name' => 'Florence'],
        ];

        foreach ($cities as $cityData) {
            $city = new City();
            $city->setName($cityData['name']);
            $city->setIsActive(true);

            $region = $this->getReference('region_' . $cityData['region'], Region::class);
            $city->setRegionId($region->getId());

            $manager->persist($city);
            $this->addReference('city_' . $cityData['name'], $city);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RegionFixtures::class];
    }
}
