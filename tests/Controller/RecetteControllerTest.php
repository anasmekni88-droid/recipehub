<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecetteControllerTest extends WebTestCase
{
    public function testRecettesPageReturns200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/recette');

        $this->assertResponseIsSuccessful();
    }

    public function testRecettesContainsCards(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/recette');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testNewRecetteRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/recette/new');

        $this->assertResponseStatusCodeSame(302);
    }

    public function testCreateRecette(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'chef@recipehub.com',
            'password' => 'chef123',
        ]);
        $client->submit($form);

        $crawler = $client->request('GET', '/recette/new');

        $form = $crawler->selectButton('Enregistrer')->form([
            'recette[titre]' => 'Pizza Test',
            'recette[description]' => 'Une très bonne pizza maison pour les tests',
            'recette[instructions]' => 'Mélanger puis cuire',
            'recette[tempsPreparation]' => 20,
            'recette[tempsCuisson]' => 15,
            'recette[nbPersonnes]' => 4,
            'recette[difficulte]' => 'facile',
            'recette[categorie]' => '1',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(303);
    }

    public function testCreateRecetteRedirectsToDrafts(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'chef@recipehub.com',
            'password' => 'chef123',
        ]);
        $client->submit($form);

        $crawler = $client->request('GET', '/recette/new');

        $form = $crawler->selectButton('Enregistrer')->form([
            'recette[titre]' => 'Burger Test',
            'recette[description]' => 'Description très longue pour test',
            'recette[instructions]' => 'Cuire et servir',
            'recette[tempsPreparation]' => 10,
            'recette[tempsCuisson]' => 20,
            'recette[nbPersonnes]' => 2,
            'recette[difficulte]' => 'moyen',
            'recette[categorie]' => '1',
        ]);

        $client->followRedirects();
        $client->submit($form);

        $this->assertSelectorTextContains('h2', 'Mes brouillons');
    }
}
