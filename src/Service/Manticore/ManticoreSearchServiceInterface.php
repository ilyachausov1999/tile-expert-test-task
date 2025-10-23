<?php

namespace App\Service\Manticore;

use App\Entity\Article;

interface ManticoreSearchServiceInterface
{
    /**
     * @param Article $article
     * @return void
     */
    public function indexArticle(Article $article): void;

    /**
     * @param string $query
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchArticles(string $query, array $filters = [], int $limit = 50, int $offset = 0): array;

    /**
     * @param int $articleId
     * @return void
     */
    public function deleteArticle(int $articleId): void;
}
