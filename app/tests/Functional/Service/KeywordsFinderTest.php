<?php

namespace App\Tests\Unit\Service;

use App\Service\KeywordsFinder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KeywordsFinderTest extends KernelTestCase
{
    private KeywordsFinder $keywordsFinder;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->keywordsFinder = static::getContainer()->get(KeywordsFinder::class);
    }

    public function testKeywordFinderReturnsFoundKeywordsCorrectlyFound(): void
    {
        $keywords = '123 la la 545, keyword 5, keyword 9';

        $response = $this->keywordsFinder
            ->setKeywordsString($keywords)
            ->setContent('11 123 la la 545 343sf keyword 5 keyword 10')
            ->findKeywordsOnContent();

        $this->assertEquals('123 la la 545, keyword 5', $response->getFoundKeywordsString());
    }

    public function testKeywordFinderReturnsEmptyStringIfKeywordsNotFound(): void
    {
        $keywords = '123 la la 545, keyword 5, keyword 9';

        $response = $this->keywordsFinder
            ->setKeywordsString($keywords)
            ->setContent('11 123 la keyword 10 keyword 20 la 5455 343sf')
            ->findKeywordsOnContent();

        $this->assertEquals('', $response->getFoundKeywordsString());
    }

    public function testKeywordFinderIgnoresCapitalLetters(): void
    {
        $keywords = 'mYkEywOrD';

        $response = $this->keywordsFinder
            ->setKeywordsString($keywords)
            ->setContent('MyKeYWoRd')
            ->findKeywordsOnContent();

        $this->assertEquals('mykeyword', $response->getFoundKeywordsString());
    }

    public function testKeywordFinderReturnsEmptyStringIfEmptyStringPassed(): void
    {
        $keywords = ' ';

        $response = $this->keywordsFinder
            ->setKeywordsString($keywords)
            ->setContent('11 123 la la 5455 343sf')
            ->findKeywordsOnContent();

        $this->assertEquals('', $response->getFoundKeywordsString());
    }
}
