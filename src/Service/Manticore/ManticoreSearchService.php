<?php

namespace App\Service\Manticore;

use App\Entity\Article;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ManticoreSearchService implements ManticoreSearchServiceInterface
{
    public function __construct(
        public HttpClientInterface $httpClient,
        public string $baseManticoreUrl
    ) {}

    /**
     * Добавление артикулов в Manticore
     *
     * @param Article $article
     * @return void
     */
    public function indexArticle(Article $article): void
    {
        $this->makeRequest('POST', '/insert', [
            'json' => [
                'index' => 'articles',
                'id' => $article->getId(),
                'doc' => $this->formatArticleForIndex($article)
            ]
        ]);
    }

    /**
     * Поиск статей по текстовому запросу
     *
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchArticles(string $query, int $limit = 50, int $offset = 0): array
    {
        $searchQuery = [
            'index' => 'articles',
            'query' => [
                'bool' => [
                    'must' => [
                        ['query_string' => ['query' => $this->prepareSearchQuery($query)]]
                    ]
                ]
            ],
            'limit' => $limit,
            'offset' => $offset
        ];

        return $this->makeRequest('POST', '/search', ['json' => $searchQuery]);

    }

    /**
     * Удаление статьи из индекса
     *
     * @param int $articleId
     * @return void
     */
    public function deleteArticle(int $articleId): void
    {
        $this->makeRequest('POST', '/delete', [
            'json' => [
                'index' => 'articles',
                'id' => $articleId
            ]
        ]);
    }

    /**
     * Подготовка статьи для индексации
     */
    private function formatArticleForIndex(Article $article): array
    {
        return [
            'sku' => $article->getSku(),
            'name' => $article->getName(),
            'description' => $article->getDescription() ?? '',
            'factory' => $article->getFactory(),
            'collection' => $article->getCollection(),
            'base_price' => (float)$article->getBasePrice(),
            'weight' => (float)$article->getWeight(),
            'is_active' => $article->isActive() ? 1 : 0,
        ];
    }

    /**
     * Подготовка поискового запроса
     *
     * @param string $query
     * @return string
     */
    private function prepareSearchQuery(string $query): string
    {
        $query = trim($query);

        if (str_word_count($query) === 1) {
            return $query . '*';
        }

        return '(' . $query . ') | (' . str_replace(' ', ' | ', $query) . ')';
    }

    /**
     * Форматирование результатов поиска
     *
     * @param array $data
     * @return array
     */
    private function formatSearchResults(array $data): array
    {
        $formatted = [
            'total' => $data['hits']['total'] ?? 0,
            'hits' => []
        ];

        if (isset($data['hits']['hits'])) {
            foreach ($data['hits']['hits'] as $hit) {
                $formatted['hits'][] = [
                    'id' => (int)$hit['_id'],
                    'score' => $hit['_score'],
                    'data' => $hit['_source']
                ];
            }
        }

        return $formatted;
    }

    private function makeRequest(string $method, string $endpoint, array $options = []): array
    {
        try {
            $url = $this->baseManticoreUrl . $endpoint;
            $response = $this->httpClient->request($method, $url, $options);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                throw new \RuntimeException("Manticore request failed: HTTP {$statusCode} for {$method} {$endpoint}");
            }

            return $this->formatSearchResults($response->toArray());

        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Manticore connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Проверка подключения к Manticore
     */
    public function checkConnection(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseManticoreUrl);
            return $response->getStatusCode() === 200;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}
