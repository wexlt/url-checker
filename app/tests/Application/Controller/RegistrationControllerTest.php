<?php

namespace App\Tests\Application\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationIsWorking(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $username = 'newuser';

        $form = $crawler->selectButton('submit')->form();
        $form['registration_form[username]']->setValue($username);
        $form['registration_form[plainPassword]']->setValue('newuserpassword');
        $form['registration_form[agreeTerms]']->setValue(1);
        $client->submit($form);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('newuser');

        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('a.logout', 'Logout');
        $this->assertSelectorTextContains('h1', 'Hello ' . $username . '!');
    }
}
