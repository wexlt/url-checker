<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WebCrawler
{
    private string $url;
    private HttpClientInterface $client;
    private string $error = '';
    private int $redirectCount = 0;
    private ?int $responseCode = null;
    private string $content = '';
    private ?ResponseInterface $response;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function crawl(): self
    {
        try {
            $this->response = $this->client->request(
                'GET',
                $this->url
            );

            $this->content = $this->response->getContent();
            $this->redirectCount = $this->response->getInfo('redirect_count');
            $this->responseCode = $this->response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            $this->error = $e->getMessage();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return $this;
    }

    public function hasError(): bool
    {
        return (bool) $this->error;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function getRedirectCount(): int
    {
        return $this->redirectCount;
    }
}