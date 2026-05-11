<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class RecetteApiTest extends ApiTestCase
{
    public function testGetRecettes(): void
    {
        $response = static::createClient()->request(
            'GET',
            '/api/recettes'
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8'
        );
    }

    public function testPostRecetteValid(): void
    {
        $client = static::createClient();
        $catResponse = $client->request('POST', '/api/categorie_recettes', [
            'headers' => ['content-type' => 'application/ld+json'],
            'json' => ['nom' => 'Test Cat', 'icone' => '🍕'],
        ]);
        $catIri = $catResponse->toArray()['@id'];

        $response = $client->request(
            'POST',
            '/api/recettes',
            [
                'headers' => ['content-type' => 'application/ld+json'],
                'json' => [
                    'titre' => 'Pizza API',
                    'description' => 'Description API très longue',
                    'instructions' => 'Instructions API',
                    'tempsPreparation' => 20,
                    'nbPersonnes' => 4,
                    'difficulte' => 'facile',
                    'categorie' => $catIri,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(201);
    }

    public function testPostRecetteInvalid(): void
    {
        $client = static::createClient();
        $catResponse = $client->request('POST', '/api/categorie_recettes', [
            'headers' => ['content-type' => 'application/ld+json'],
            'json' => ['nom' => 'Test Cat 2', 'icone' => '🥗'],
        ]);
        $catIri = $catResponse->toArray()['@id'];

        $client->request(
            'POST',
            '/api/recettes',
            [
                'headers' => ['content-type' => 'application/ld+json'],
                'json' => [
                    'titre' => '',
                    'tempsPreparation' => 20,
                    'nbPersonnes' => 4,
                    'difficulte' => 'facile',
                    'categorie' => $catIri,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }
}
