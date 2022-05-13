<?php

namespace App\tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    private $client = null;

    public function testIndex(): void
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/');
        static::assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testFormIndex(): void
    {
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@mail.com');

        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/produit/modification/1');
        $buttonCrawlerNode = $crawler->selectButton('update');
        $form = $buttonCrawlerNode->form();
        $form['product[title]'] = 'test';
        $form['product[image]'] = 'image.jpg';
        $form['product[price]'] = 10;
        $this->client->submit($form);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testProduct(): void
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/produit/1');
        static::assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testUpdate(): void
    {
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@mail.com');

        $this->client->loginUser($testUser);
        $this->client->request('GET', '/produit/modification/1');
        static::assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testDelete(): void
    {
        $this->client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@mail.com');

        $this->client->loginUser($testUser);
        $this->client->request('GET', '/produit/suppression/1');
        static::assertEquals(
            Response::HTTP_FOUND,
            $this->client->getResponse()->getStatusCode()
        );
    }
}
