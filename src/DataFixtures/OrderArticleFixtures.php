<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\OrderArticle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $articleCounter = 0;

        for ($orderIndex = 0; $orderIndex < 30; $orderIndex++) {
            $articlesCount = $faker->numberBetween(1, 5);

            for ($j = 0; $j < $articlesCount; $j++) {
                $orderArticle = new OrderArticle();
                $orderArticle->setOrder($this->getReference('order_' . $orderIndex, Order::class));
                $article = $this->getReference('article_' . $faker->numberBetween(0, 49), Article::class);
                $orderArticle->setArticleId($article->getId());
                $amount = $faker->randomFloat(3, 1, 50);
                $orderArticle->setAmount((string)$amount);
                $basePrice = (float) $article->getBasePrice();
                $price = $faker->randomFloat(2, $basePrice * 0.9, $basePrice * 1.1);
                $orderArticle->setPrice((string)$price);

                $orderArticle->setDisplayMeasure("m2");
                $orderArticle->setConversionRate((string)$faker->randomFloat(6, 0.5, 2.0));

                $weight = (float) $article->getWeight();
                $orderArticle->setWeight((string)$weight);

                $orderArticle->setSpecialNotes($faker->boolean(30) ? $faker->sentence() : null);

                $manager->persist($orderArticle);
                $this->addReference('order_article_' . $articleCounter, $orderArticle);
                $articleCounter++;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrderFixtures::class,
            ArticleFixtures::class,
        ];
    }
}
