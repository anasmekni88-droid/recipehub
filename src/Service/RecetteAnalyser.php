<?php

namespace App\Service;

use App\Entity\Recette;
use App\Repository\RecetteRepository;

class RecetteAnalyser
{
    public function __construct(
        private RecetteRepository $repo
    ) {}

    /**
     * Retourne le temps total de préparation + cuisson
     */
    public function getTempsTotal(Recette $r): int
    {
        return $r->getTempsPreparation() + ($r->getTempsCuisson() ?? 0);
    }

    /**
     * Retourne le nombre total des recettes publiées
     */
    public function getTotalRecettesPubliees(): int
    {
        return $this->repo->count([
            'publiee' => true
        ]);
    }

    /**
     * Retourne le nombre de recettes par catégorie
     */
    public function getRecettesParCategorie(): array
    {
        $recettes = $this->repo->findPublished();

        $stats = [];

        foreach ($recettes as $recette) {

            $categorie = $recette->getCategorie()?->getNom();

            if (!$categorie) {
                $categorie = 'Sans catégorie';
            }

            if (!isset($stats[$categorie])) {
                $stats[$categorie] = 0;
            }

            $stats[$categorie]++;
        }

        return $stats;
    }

    /**
     * Moyenne du nombre d'ingrédients par recette
     */
    public function getMoyenneIngredients(): float
    {
        $recettes = $this->repo->findPublished();

        if (count($recettes) === 0) {
            return 0;
        }

        $totalIngredients = 0;

        foreach ($recettes as $recette) {
            $totalIngredients += $recette->getIngredients()->count();
        }

        return round(
            $totalIngredients / count($recettes),
            2
        );
    }
}