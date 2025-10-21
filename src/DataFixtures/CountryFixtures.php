<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = [
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Italy', 'code' => 'IT'],
            ['name' => 'Spain', 'code' => 'ES'],
            ['name' => 'Poland', 'code' => 'PL'],
        ];

        foreach ($countries as $countryData) {
            $country = new Country();
            $country->setName($countryData['name']);
            $country->setCode($countryData['code']);
            $country->setIsActive(true);

            $manager->persist($country);
            $this->addReference('country_' . $countryData['code'], $country);
        }

        $manager->flush();
    }
}
