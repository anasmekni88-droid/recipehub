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
        $response = static::createClient()->request(
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
                    'categorie' => '/api/categorie_recettes/1',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(201);
    }

    public function testPostRecetteInvalid(): void
    {
        static::createClient()->request(
            'POST',
            '/api/recettes',
            [
                'headers' => ['content-type' => 'application/ld+json'],
                'json' => [
                    'titre' => '',
                    'tempsPreparation' => 20,
                    'nbPersonnes' => 4,
                    'difficulte' => 'facile',
                    'categorie' => '/api/categorie_recettes/1',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }
}
