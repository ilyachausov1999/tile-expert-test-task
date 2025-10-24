<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Order;
use App\Entity\OrderDelivery;
use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderDeliveryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 25; $i++) {
            $delivery = new OrderDelivery();
            $delivery->setOrder($this->getReference('order_' . $i, Order::class));

            if ($faker->boolean(80)) {
                $city = $this->getReference('city_' . $faker->randomElement([
                        'Munich', 'Nuremberg', 'Cologne', 'Dusseldorf', 'Paris', 'Marseille', 'Milan', 'Florence'
                    ]), City::class);
                $delivery->setCityId($city->getId());

                $region = $this->getReference('region_' . $faker->randomElement(['BY', 'NW', 'IDF', 'PAC', 'LOM', 'TUS']), Region::class);
                $delivery->setRegionId($region->getId());

                $country = $this->getReference('country_' . $faker->randomElement(['DE', 'FR', 'IT']), Country::class);
                $delivery->setCountryId($country->getId());
            }

            $delivery->setAmount((string)$faker->randomFloat(2, 50, 500));
            $delivery->setTypeId((bool)$faker->numberBetween(0, 1));
            $delivery->setCalculateTypeId((bool)$faker->numberBetween(0, 1));

            $startDate = $faker->dateTimeBetween('+7 days', '+15 days');
            $endDate = $faker->dateTimeBetween('+16 days', '+30 days');
            $delivery->setTimeMin($startDate);
            $delivery->setTimeMax($endDate);

            $delivery->setFullAddress($faker->address());
            $delivery->setAddress($faker->streetAddress());
            $delivery->setBuilding($faker->boolean(60) ? $faker->buildingNumber() : null);
            $delivery->setApartmentOffice($faker->boolean(70) ? $faker->numberBetween(1, 100) . $faker->randomElement(['', 'A', 'B', 'C']) : null);
            $delivery->setPostalCode($faker->postcode());
            $delivery->setTrackingNumber($faker->boolean(50) ? $faker->uuid() : null);
            $delivery->setCarrierId($faker->boolean(60) ? $faker->numberBetween(1, 10) : null);
            $delivery->setOffsetReason(true);
            $delivery->setOffsetDate($faker->boolean(15) ? $faker->dateTimeBetween('-5 days', '+5 days') : null);
            $delivery->setProposedDate($faker->boolean(70) ? $faker->dateTimeBetween('+10 days', '+25 days') : null);
            $delivery->setShipDate($faker->boolean(40) ? $faker->dateTimeBetween('-10 days', '+5 days') : null);

            $manager->persist($delivery);
            $this->addReference('delivery_' . $i, $delivery);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrderFixtures::class,
            CountryFixtures::class,
            RegionFixtures::class,
            CityFixtures::class,
        ];
    }
}
