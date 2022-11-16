<?php

namespace App\Tests\Unit\Service;

use App\Service\WebCrawler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WebCrawlerTest extends KernelTestCase
{
    private WebCrawler $webCrawler;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->webCrawler = static::getContainer()->get(WebCrawler::class);
    }

    public function testCrawlerCatchesException(): void
    {
        $this->webCrawler->setUrl('http://localhostsdsd:9898989')->crawl();

        $this->assertEquals(true, $this->webCrawler->hasError());
        $this->assertEquals(null, $this->webCrawler->getResponseCode());
        $this->assertEquals(0, $this->webCrawler->getRedirectCount());
        $this->assertEquals(0, 0);
    }

    public function testCrawlerReturnsRightResponses(): void
    {
        $this->webCrawler->setUrl('http://host.docker.internal:8080')->crawl();

        $this->assertEquals(false, $this->webCrawler->hasError());
        $this->assertEquals(200, $this->webCrawler->getResponseCode());
        $this->assertEquals(0, $this->webCrawler->getRedirectCount());

        $this->webCrawler->setUrl('http://host.docker.internal:8080/link')->crawl();

        $this->assertEquals(false, $this->webCrawler->hasError());
        $this->assertEquals(200, $this->webCrawler->getResponseCode());
        $this->assertEquals(1, $this->webCrawler->getRedirectCount());
    }
}
