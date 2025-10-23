<?php

namespace App\Command;

use App\Entity\Article;
use App\Service\Manticore\ManticoreSearchServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:index-articles',
    description: 'Index all articles in Manticore Search'
)]
class IndexArticlesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ManticoreSearchServiceInterface $searchService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Index all articles in Manticore Search');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->searchService->checkConnection()) {
            $io->error('Manticore is not available');
            return Command::FAILURE;
        }

        $articles = $this->entityManager->getRepository(Article::class)->findAll();

        if (empty($articles)) {
            $io->warning('No articles found in database');
            return Command::SUCCESS;
        }

        $io->progressStart(count($articles));

        $indexedCount = 0;
        foreach ($articles as $article) {
            try {
                $this->searchService->indexArticle($article);
                $indexedCount++;
            } catch (\Exception $e) {
                $io->warning("Failed to index article {$article->getId()}: {$e->getMessage()}");
            }
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success(sprintf('Successfully indexed %d/%d articles', $indexedCount, count($articles)));

        return Command::SUCCESS;
    }
}
