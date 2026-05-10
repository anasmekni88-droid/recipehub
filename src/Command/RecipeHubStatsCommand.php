<?php

namespace App\Command;

use App\Repository\CategorieRecetteRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:recipehub:stats',
    description: 'Affiche les statistiques de la plateforme de recettes',
)]
class RecipeHubStatsCommand extends Command
{
    public function __construct(
        private RecetteRepository $recetteRepository,
        private CategorieRecetteRepository $categorieRecetteRepository,
        private IngredientRepository $ingredientRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'detail',
                null,
                InputOption::VALUE_NONE,
                'Afficher les détails par catégorie'
            )
            ->addOption(
                'top',
                null,
                InputOption::VALUE_REQUIRED,
                'Afficher le top N des recettes les plus longues',
                3
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('📊 Statistiques RecipeHub');

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES GLOBALES
        |--------------------------------------------------------------------------
        */

        $totalRecettes = $this->recetteRepository->count([]);

        $publishedRecettes = $this->recetteRepository->count([
            'publiee' => true
        ]);

        $draftRecettes = $this->recetteRepository->count([
            'publiee' => false
        ]);

        $totalIngredients = $this->ingredientRepository->count([]);

        $recettes = $this->recetteRepository->findAll();

        $totalTempsPreparation = 0;

        foreach ($recettes as $recette) {
            $totalTempsPreparation += $recette->getTempsPreparation();
        }

        $tempsMoyen = count($recettes) > 0
            ? round($totalTempsPreparation / count($recettes), 2)
            : 0;

        $io->section('📌 Statistiques globales');

        $io->table(
            ['Statistique', 'Valeur'],
            [
                ['Total recettes', $totalRecettes],
                ['Recettes publiées', $publishedRecettes],
                ['Recettes brouillons', $draftRecettes],
                ['Total ingrédients', $totalIngredients],
                ['Temps moyen de préparation', $tempsMoyen . ' min'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | RÉPARTITION PAR CATÉGORIE
        |--------------------------------------------------------------------------
        */

        $io->section('📂 Répartition par catégorie');

        $categories = $this->categorieRecetteRepository->findAll();

        $categoryStats = [];

        foreach ($categories as $category) {
            $categoryStats[] = [
                $category->getIcone() . ' ' . $category->getNom(),
                count($category->getRecettes())
            ];
        }

        $io->table(
            ['Catégorie', 'Nombre de recettes'],
            $categoryStats
        );

        /*
        |--------------------------------------------------------------------------
        | RÉPARTITION PAR DIFFICULTÉ
        |--------------------------------------------------------------------------
        */

        $io->section('⭐ Répartition par difficulté');

        $facile = $this->recetteRepository->count([
            'difficulte' => 'facile'
        ]);

        $moyen = $this->recetteRepository->count([
            'difficulte' => 'moyen'
        ]);

        $difficile = $this->recetteRepository->count([
            'difficulte' => 'difficile'
        ]);

        $io->table(
            ['Difficulté', 'Nombre'],
            [
                ['🟢 Facile', $facile],
                ['🟡 Moyen', $moyen],
                ['🔴 Difficile', $difficile],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | TOP AUTEURS
        |--------------------------------------------------------------------------
        */

        $io->section('👨‍🍳 Top auteurs');

        $authors = [];

        foreach ($recettes as $recette) {
            $auteur = $recette->getAuteur();

            if (!$auteur) {
                continue;
            }

            $pseudo = $auteur->getPseudo();

            if (!isset($authors[$pseudo])) {
                $authors[$pseudo] = 0;
            }

            $authors[$pseudo]++;
        }

        arsort($authors);

        $topAuthors = [];

        $i = 0;

        foreach ($authors as $pseudo => $count) {
            if ($i >= 3) {
                break;
            }

            $topAuthors[] = [$pseudo, $count];

            $i++;
        }

        $io->table(
            ['Auteur', 'Nombre de recettes'],
            $topAuthors
        );

        /*
        |--------------------------------------------------------------------------
        | OPTION --detail
        |--------------------------------------------------------------------------
        */

        if ($input->getOption('detail')) {

            $io->section('📋 Détails des catégories');

            $details = [];

            foreach ($categories as $category) {

                $details[] = [
                    $category->getNom(),
                    $category->getDescription() ?? 'Aucune description',
                    count($category->getRecettes())
                ];
            }

            $io->table(
                ['Catégorie', 'Description', 'Recettes'],
                $details
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OPTION --top=N
        |--------------------------------------------------------------------------
        */

        $topLimit = (int) $input->getOption('top');

        usort($recettes, function ($a, $b) {

            $tempsA = $a->getTempsPreparation()
                + ($a->getTempsCuisson() ?? 0);

            $tempsB = $b->getTempsPreparation()
                + ($b->getTempsCuisson() ?? 0);

            return $tempsB <=> $tempsA;
        });

        $topRecettes = array_slice($recettes, 0, $topLimit);

        $topTable = [];

        foreach ($topRecettes as $recette) {

            $tempsTotal = $recette->getTempsPreparation()
                + ($recette->getTempsCuisson() ?? 0);

            $topTable[] = [
                $recette->getTitre(),
                $tempsTotal . ' min'
            ];
        }

        $io->section("🔥 Top {$topLimit} recettes les plus longues");

        $io->table(
            ['Recette', 'Temps total'],
            $topTable
        );

        $io->success('Statistiques générées avec succès ✅');

        return Command::SUCCESS;
    }
}