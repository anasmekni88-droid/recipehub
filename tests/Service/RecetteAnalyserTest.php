<?php

namespace App\Tests\Service;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Service\RecetteAnalyser;
use PHPUnit\Framework\TestCase;

class RecetteAnalyserTest extends TestCase
{
    public function testGetTempsTotal(): void
    {
        $repo = $this->createStub(RecetteRepository::class);

        $service = new RecetteAnalyser($repo);

        $recette = new Recette();
        $recette->setTempsPreparation(20);
        $recette->setTempsCuisson(40);

        $this->assertEquals(60, $service->getTempsTotal($recette));
    }

    public function testGetTempsTotalWithoutCuisson(): void
    {
        $repo = $this->createStub(RecetteRepository::class);

        $service = new RecetteAnalyser($repo);

        $recette = new Recette();
        $recette->setTempsPreparation(15);
        $recette->setTempsCuisson(null);

        $this->assertEquals(15, $service->getTempsTotal($recette));
    }

    public function testGetTotalRecettesPubliees(): void
    {
        $repo = $this->createMock(RecetteRepository::class);

        $repo
            ->expects($this->once())
            ->method('count')
            ->with(['publiee' => true])
            ->willReturn(5);

        $service = new RecetteAnalyser($repo);

        $this->assertSame(5, $service->getTotalRecettesPubliees());
    }

    public function testGetMoyenneIngredientsReturnsZero(): void
    {
        $repo = $this->createStub(RecetteRepository::class);

        $repo
            ->method('findPublished')
            ->willReturn([]);

        $service = new RecetteAnalyser($repo);

        $this->assertSame(0.0, $service->getMoyenneIngredients());
    }
}
