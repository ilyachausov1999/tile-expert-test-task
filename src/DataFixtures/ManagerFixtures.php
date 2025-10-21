<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Manager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ManagerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $managers = [
            ['username' => 'admin', 'role' => true],
            ['username' => 'manager1', 'role' => false],
            ['username' => 'manager2', 'role' => false],
        ];

        foreach ($managers as $managerData) {
            $m = new Manager();
            $m->setManagerName($faker->name());
            $m->setManagerEmail($faker->email());
            $m->setManagerPhone($faker->phoneNumber());
            $m->setPasswordHash(password_hash('password123', PASSWORD_DEFAULT));
            $m->setUsername($managerData['username']);
            $m->setRole($managerData['role']);
            $m->setIsActive(true);

            $manager->persist($m);
            $this->addReference('manager_' . $managerData['username'], $m);
        }

        $manager->flush();
    }
}
