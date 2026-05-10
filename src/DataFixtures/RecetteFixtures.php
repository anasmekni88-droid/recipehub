<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Entity\User;
use App\Entity\CategorieRecette;
use App\Entity\TagRecette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RecetteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $difficultes = ['facile', 'moyen', 'difficile'];

        for ($i = 1; $i <= 20; $i++) {

            $recette = new Recette();

            $recette->setTitre($faker->sentence(3));
            $recette->setDescription($faker->paragraph(2));
            $recette->setInstructions($faker->paragraphs(3, true));

            $recette->setTempsPreparation(rand(10, 60));
            $recette->setTempsCuisson(rand(10, 120));

            $recette->setDifficulte($difficultes[array_rand($difficultes)]);
            $recette->setNbPersonnes(rand(1, 8));

            $recette->setDateCreation(new \DateTimeImmutable());
            $recette->setPubliee((bool) rand(0, 1));
            

            // AUTHOR
            $authorKey = rand(0, 5) === 0 ? 'user-admin' : 'user-chef';

            $recette->setAuteur(
                $this->getReference($authorKey, User::class)
            );

            // CATEGORY
            $recette->setCategorie(
                $this->getReference(
                    'categorie-' . rand(0, 5),
                    CategorieRecette::class
                )
            );

            // TAGS (1–4)
            for ($j = 0; $j < rand(1, 4); $j++) {
                $recette->addTag(
                    $this->getReference(
                        'tag-' . rand(0, 7),
                        TagRecette::class
                    )
                );
            }
            // INGREDIENTS (1–5)
            $ingredientNames = ['Farine', 'Sucre', 'Œufs', 'Beurre', 'Lait', 'Chocolat', 'Sel', 'Huile', 'Levure', 'Pommes'];
            $quantites = ['500g', '200g', '3', '100g', '250ml', '150g', '1 pincée', '2 c. à soupe', '1 sachet', '4'];
            for ($k = 0; $k < rand(1, 5); $k++) {
                $idx = rand(0, 9);
                $ingredient = new Ingredient();
                $ingredient->setNom($ingredientNames[$idx]);
                $ingredient->setQuantite($quantites[$idx]);
                $ingredient->setRecette($recette);
                $manager->persist($ingredient);
            }

            $manager->persist($recette);
            $this->addReference('recette-' . $i, $recette);
        }

        $manager->flush();
    }

  public function getDependencies(): array
{
    return [
        UserFixtures::class,
        CategorieRecetteFixtures::class,
        TagRecetteFixtures::class,
    ];
}
}