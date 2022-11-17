<?php

namespace App\Service;

use App\Entity\LinkLog;
use App\Entity\Link;
use App\Repository\LinkRepository;
use App\Repository\LinkLogRepository;

class LinkFetch
{
    public function __construct(
        private LinkRepository $linkRepository,
        private LinkLogRepository $linkLogRepository,
        private WebCrawler $crawler,
        private KeywordsFinder $keywordsFinder,
        private bool $withKeywordsCheck = false
    ) {
    }

    public function fetchAll(): self
    {
        $links = $this->linkRepository->findAll();

        $this->crawl($links);

        return $this;
    }

    public function withKeywordsCheck(): self
    {
        $this->withKeywordsCheck = true;

        return $this;
    }

    private function crawl(Array $links): void
    {
        foreach ($links as $link) {
            $this->crawler->setUrl($link->getUrl())->crawl();

            $log = $this->crawler->hasError() ? $this->crawler->getError() : $this->crawler->getContent();

            $linkLog = new LinkLog();

            $linkLog->setLog($log);
            $linkLog->setResponseCode($this->crawler->getResponseCode());
            $linkLog->setRedirectsCount($this->crawler->getRedirectCount());
            $linkLog->setHasError($this->crawler->hasError() ? 1 : 0);
            $linkLog->setLink($link);
            $linkLog->setDatetimeCreated(new \DateTime());

            if ($this->withKeywordsCheck && ! $this->crawler->hasError()) {
                $keywordsFound = $this->findKeywords($link->getKeywords());
                $linkLog->setKeywordsFound($keywordsFound);
            }

            $this->linkLogRepository->save($linkLog, true);
        }
    }

    private function findKeywords(string|null $keywords): string|null
    {
        if (!$keywords) {
            return null;
        }

        $keywordsFound = $this->keywordsFinder
            ->setContent($this->crawler->getContent())
            ->setKeywordsString($keywords)
            ->findKeywordsOnContent()
            ->getFoundKeywordsString();

        if ($keywordsFound) {
            return $keywordsFound;
        }

        return null;
    }
}
