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

        $form = $crawler->selectButton('Enregistrer')->form();
        $form['recette[titre]'] = 'Pizza Test';
        $form['recette[description]'] = 'Une très bonne pizza maison pour les tests';
        $form['recette[instructions]'] = 'Mélanger puis cuire';
        $form['recette[tempsPreparation]'] = 20;
        $form['recette[tempsCuisson]'] = 15;
        $form['recette[nbPersonnes]'] = 4;
        $form['recette[difficulte]'] = 'facile';
        $catOptions = array_filter($form['recette[categorie]']->availableOptionValues());
        $form['recette[categorie]']->select((string) reset($catOptions));

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

        $form = $crawler->selectButton('Enregistrer')->form();
        $form['recette[titre]'] = 'Burger Test';
        $form['recette[description]'] = 'Description très longue pour test';
        $form['recette[instructions]'] = 'Cuire et servir';
        $form['recette[tempsPreparation]'] = 10;
        $form['recette[tempsCuisson]'] = 20;
        $form['recette[nbPersonnes]'] = 2;
        $form['recette[difficulte]'] = 'moyen';
        $catOptions = array_filter($form['recette[categorie]']->availableOptionValues());
        $form['recette[categorie]']->select((string) reset($catOptions));

        $client->followRedirects();
        $client->submit($form);

        $this->assertSelectorTextContains('h2', 'Mes brouillons');
    }
}
