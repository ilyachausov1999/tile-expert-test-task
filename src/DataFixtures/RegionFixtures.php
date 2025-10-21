<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RegionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $regions = [
            ['country' => 'DE', 'name' => 'Bavaria', 'code' => 'BY'],
            ['country' => 'DE', 'name' => 'North Rhine-Westphalia', 'code' => 'NW'],
            ['country' => 'FR', 'name' => 'Paris', 'code' => 'IDF'],
            ['country' => 'FR', 'name' => 'Provence', 'code' => 'PAC'],
            ['country' => 'IT', 'name' => 'Lombardy', 'code' => 'LOM'],
            ['country' => 'IT', 'name' => 'Tuscany', 'code' => 'TUS'],
        ];

        foreach ($regions as $regionData) {
            $region = new Region();
            $region->setName($regionData['name']);
            $region->setCode($regionData['code']);
            $region->setIsActive(true);

            $country = $this->getReference('country_' . $regionData['country'], Country::class);
            $region->setCountryId($country->getId());

            $manager->persist($region);
            $this->addReference('region_' . $regionData['code'], $region);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CountryFixtures::class];
    }
}
