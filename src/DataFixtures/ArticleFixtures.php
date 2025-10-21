<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $factories = ['Porcelanosa', 'Marazzi', 'Imola', 'Ragno', 'Casalgrande'];
        $collections = ['Modern', 'Classic', 'Premium', 'Eco', 'Luxury'];

        for ($i = 0; $i < 50; $i++) {
            $article = new Article();
            $article->setSku('SKU-' . str_pad((string)($i + 1), 6, '0', STR_PAD_LEFT));
            $article->setName($faker->words(3, true) . ' Tile');
            $article->setDescription($faker->boolean(70) ? $faker->paragraph() : null);
            $article->setFactory($faker->randomElement($factories));
            $article->setCollection($faker->randomElement($collections));
            $article->setPallet($faker->numberBetween(20, 100));
            $article->setPackaging($faker->numberBetween(1, 10));
            $article->setPackagingCount($faker->numberBetween(1, 5));
            $article->setMultiplePallet(false);
            $article->setIsSwimmingPool($faker->boolean(20));
            $article->setBasePrice((string)$faker->randomFloat(2, 10, 100));
            $article->setBaseMeasure($faker->randomElement(['pcs', 'm2', 'box']));
            $article->setWeight((string)$faker->randomFloat(3, 0.5, 5.0));
            $article->setIsActive($faker->boolean(85));

            $manager->persist($article);
            $this->addReference('article_' . $i, $article);
        }

        $manager->flush();
    }
}
