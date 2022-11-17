<?php

namespace App\Command;

use App\Entity\LinkLog;
use App\Repository\LinkLogRepository;
use App\Repository\LinkRepository;
use App\Service\KeywordsFinder;
use App\Service\LinkFetch;
use App\Service\WebCrawler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-links',
    description: 'Add a short description for your command',
)]
class CheckLinksCommand extends Command
{
    public function __construct(private LinkFetch $linkFetch)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Checks all the links - their url statuses and keywords');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->linkFetch->withKeywordsCheck()->fetchAll();

        $io = new SymfonyStyle($input, $output);

        $io->success('Command ran successfully');

        return Command::SUCCESS;
    }
}
