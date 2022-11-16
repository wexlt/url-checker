<?php

namespace App\Tests\Application\Controller;

use App\DataFixtures\UserFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class HomeControllerTest extends WebTestCase
{
    public function testVisitingWhileLoggedOut(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.login', 'Login');
        $this->assertSelectorTextContains('a.signup', 'Signup');
    }

    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('testuser');

        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.logout', 'Logout');
        $this->assertSelectorTextContains('h1', 'Hello testuser!');
    }
}
