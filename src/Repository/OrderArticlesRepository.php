<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Dto\ArticleDataDto;
use App\Entity\Order;
use App\Entity\OrderArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderArticlesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderArticle::class);
    }

    /**
     * @param Order $order
     * @param array $articles
     * @return void
     */
    public function createOrderArticles(Order $order, array $articles): void
    {
        /** @var  $articleData  ArticleDataDto*/
        foreach ($articles as $articleData) {
            $orderArticle = new OrderArticle();
            $orderArticle->setOrder($order);
            $orderArticle->setArticleId($articleData->articleId);
            $orderArticle->setAmount($articleData->amount);
            $orderArticle->setPrice((string)$articleData->articleId);
            $orderArticle->setDisplayMeasure($articleData->displayMeasure);
            $orderArticle->setSpecialNotes($articleData->specialNotes);
            $orderArticle->setWeight('5.00'); // Можно вычислить из Article entity
            $orderArticle->setConversionRate('1.00');

            $this->getEntityManager()->persist($orderArticle);
        }
    }
}
