<?php

namespace App\Command;

use App\Entity\LinkLog;
use App\Repository\LinkLogRepository;
use App\Repository\LinkRepository;
use App\Service\KeywordsFinder;
use App\Service\WebCrawler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:check-links',
    description: 'Add a short description for your command',
)]
class CheckLinksCommand extends Command
{
    private LinkRepository $linkRepository;
    private LinkLogRepository $linkLogRepository;

    public function __construct(LinkRepository $linkRepository, LinkLogRepository $linkLogRepository)
    {
        $this->linkRepository = $linkRepository;
        $this->linkLogRepository = $linkLogRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Checks all the links - their url statuses and keywords');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $links = $this->linkRepository->findAll();

        $io = new SymfonyStyle($input, $output);

        if (!count($links)) {
            $io->success('No Links found');

            return Command::SUCCESS;
        }

        foreach ($links as $link) {
            $crawler = new WebCrawler();

            $crawler->setUrl($link->getUrl())->crawl();

            if ($crawler->hasError()) {
                $log = 'Error: ' . $crawler->getError();
            } else {
                $log = 'Code: ' . $crawler->getResponseCode();
                $log .=  '. Redirects count: ' . $crawler->getRedirectCount();

                $keywordsFound = $this->getKeywordsWasFound($link->getKeywords(), $crawler->getContent());

                $log .= '. Keywords found: ' . ($keywordsFound ?: 'No keywords found');
            }

            $linkLog = new LinkLog();
            $linkLog->setLog($log);
            $linkLog->setLink($link);
            $linkLog->setDatetimeCreated(new \DateTime());
            $this->linkLogRepository->save($linkLog, true);
        }


        $io->success('Command ran successfully');

        return Command::SUCCESS;
    }

    private function getKeywordsWasFound(string $keywords, string $content): string
    {
        if (!$keywords) {
            return '';
        }

        $keywordsFinder = new KeywordsFinder();

        return $keywordsFinder
            ->setContent($content)
            ->setKeywordsString($keywords)
            ->findKeywordsOnContent()
            ->getFoundKeywordsString();
    }
}
