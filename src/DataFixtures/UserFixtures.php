<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPhone($faker->phoneNumber());
            $user->setCountryCode($faker->randomElement(['DE', 'FR', 'IT', 'ES']));
            $user->setVatType($faker->boolean(30));
            $user->setVatNumber('20');
            $user->setTaxNumber('20');
            $user->setSex(true);
            $user->setClientName($faker->firstName());
            $user->setClientSurname($faker->lastName());
            $user->setCompanyName($faker->boolean(40) ? $faker->company() : null);
            $user->setPasswordHash(password_hash('password123', PASSWORD_DEFAULT));
            $user->setIsActive($faker->boolean(90));

            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
