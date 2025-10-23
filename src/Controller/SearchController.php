<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Service\Manticore\ManticoreSearchServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: "Search articles")]
class SearchController extends AbstractController
{
    public function __construct(
        private readonly  ManticoreSearchServiceInterface $searchService
    ) {}

    #[Route('/api/search/articles', name: 'api_search_articles', methods: ['GET'])]
    #[OA\Get(
        description: 'Search articles using full-text search with filters',
        summary: 'Search articles'
    )]
    #[OA\Parameter(
        name: 'q',
        description: 'Search query text',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string', example: 'плитка керамическая')
    )]
    #[OA\Parameter(
        name: 'factory',
        description: 'Filter by factory/manufacturer',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string', example: 'abk')
    )]
    #[OA\Parameter(
        name: 'collection',
        description: 'Filter by collection name',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string', example: 'poetry-net')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Number of results to return (default: 50, max: 100)',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 50, maximum: 100, minimum: 1)
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Number of results to skip for pagination',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 0, minimum: 0)
    )]
    #[OA\Response(
        response: 200,
        description: 'Search completed successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'total', type: 'integer', example: 25),
                        new OA\Property(
                            property: 'hits',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 123),
                                    new OA\Property(property: 'score', type: 'number', format: 'float', example: 1.5),
                                    new OA\Property(
                                        property: 'data',
                                        properties: [
                                            new OA\Property(property: 'sku', type: 'string', example: '17562-multi-grey'),
                                            new OA\Property(property: 'name', type: 'string', example: 'Керамическая плитка'),
                                            new OA\Property(property: 'factory', type: 'string', example: 'abk'),
                                            new OA\Property(property: 'collection', type: 'string', example: 'poetry-net'),
                                            new OA\Property(property: 'base_price', type: 'number', format: 'float', example: 1500.50)
                                        ]
                                    )
                                ]
                            )
                        )
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation error - missing search query'
    )]
    #[OA\Response(
        response: 500,
        description: 'Search service unavailable'
    )]
    public function searchArticles(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $factory = $request->query->get('factory', '');
        $collection = $request->query->get('collection', '');
        $limit = $request->query->getInt('limit', 50);
        $offset = $request->query->getInt('offset', 0);

        if (empty($query)) {
            return $this->json([
                'success' => false,
                'error' => 'Search query is required'
            ], 400);
        }

        $limit = min($limit, 100);

        $filters = [];
        if (!empty($factory)) {
            $filters['factory'] = $factory;
        }
        if (!empty($collection)) {
            $filters['collection'] = $collection;
        }

        try {
            $results = $this->searchService->searchArticles($query, $filters, $limit, $offset);

            return $this->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
    #[Route('/api/search/articles/{id}', name: 'api_delete_article', methods: ['DELETE'])]
    #[OA\Delete(
        description: "Delete article",
        summary: 'Delete article'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Article ID to delete from search index',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 123)
    )]
    #[OA\Response(
        response: 200,
        description: 'Article successfully deleted from search index',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Article deleted from search index')
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Article not found in search index'
    )]
    #[OA\Response(
        response: 500,
        description: 'Failed to delete article from search index'
    )]
    public function deleteArticle(int $id): JsonResponse
    {
        try {
            $this->searchService->deleteArticle($id);

            return $this->json([
                'success' => true,
                'message' => 'Article deleted from search index'
            ]);

        } catch (\Exception $e) {
            $statusCode = str_contains($e->getMessage(), '404') ? 404 : 500;

            return $this->json([
                'success' => false,
                'error' => 'Failed to delete article: ' . $e->getMessage()
            ], $statusCode);
        }
    }
}
