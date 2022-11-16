<?php

namespace App\Tests\Application\Controller;

use App\Entity\Link;
use App\Repository\LinkRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private LinkRepository $repository;
    private string $path = '/link/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Link::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
        $userRepository = static::getContainer()->get(UserRepository::class);

        $this->testUser = $userRepository->findOneByUsername('testuser');
    }

    public function testUserCantAccessPageWhileNotLoggedIn(): void
    {
        $this->client->request('GET', $this->path);
        $router = $this->client->getContainer()->get('router');
        $loginUrl = $router->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertResponseRedirects($loginUrl, 302);
    }

    public function testUserCantCreateLinkPageWhileNotLoggedIn(): void
    {
        $this->client->request('POST', $this->path . 'new', [
            'url' => 'https://google.com',
            'keywords' => 'google, keyword, something'
        ]);

        $router = $this->client->getContainer()->get('router');
        $loginUrl = $router->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->assertResponseRedirects($loginUrl, 302);
    }

    public function testUserCanCreateLinkPageWhileLoggedIn(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->loginUser($this->testUser);

        $params = [
            'url' => 'https://google.com',
            'keywords' => 'google, keyword, something'
        ];

        $this->client->request('GET', $this->path . 'new');

        $this->client->submitForm('Save', [
            'link[url]' => $params['url'],
            'link[keywords]' => $params['keywords'],
        ]);

        $this->assertResponseRedirects('/link/');

        $link = static::getContainer()->get(LinkRepository::class)
            ->findOneBy(['url' => $params['url']]);

        $this->assertSame($params['keywords'], $link->getKeywords());

        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testRemoveLink(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Link();
        $fixture->setUrl('https://any.com');
        $fixture->setKeywords('one, two, three');
        $fixture->setUser($this->testUser);

        $this->repository->save($fixture, true);

        $this->assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->loginUser($this->testUser);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        $this->assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        $this->assertResponseRedirects('/link/');
    }

    public function testEditLink(): void
    {
        $fixture = new Link();
        $fixture->setUrl('https://any.com');
        $fixture->setKeywords('one, two, three');
        $fixture->setUser($this->testUser);

        $this->repository->save($fixture, true);

        $this->client->loginUser($this->testUser);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $params = [
            'link[url]' => 'https://new.com',
            'link[keywords]' => 'four'
        ];

        $this->client->submitForm('Update', $params);

        $this->assertResponseRedirects('/link/');

        $fixture = $this->repository->findAll();

        $this->assertSame($params['link[url]'], $fixture[0]->getUrl());
        $this->assertSame($params['link[keywords]'], $fixture[0]->getKeywords());
    }

    public function testLinkUrlValidationIsWorking(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());
        $this->client->loginUser($this->testUser);

        $params = [
            'url' => 'anywronglink',
            'keywords' => 'google, keyword, something'
        ];

        $this->client->request('GET', $this->path . 'new');

        $this->client->submitForm('Save', [
            'link[url]' => $params['url'],
            'link[keywords]' => $params['keywords'],
        ]);

        $this->assertResponseIsUnprocessable();

        $this->assertSelectorTextContains('li', 'Url is not valid.');

        $this->assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
    }
}
