<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Retrieve all recipes that were created by RecetteFixtures
        $recettes = $manager->getRepository(Recette::class)->findAll();

        foreach ($recettes as $recette) {
            // Generate between 3 and 8 ingredients per recipe
            $nbIngredients = $faker->numberBetween(3, 8);
            
            for ($i = 0; $i < $nbIngredients; $i++) {
                $ingredient = new Ingredient();
                
                // You can customize the Faker methods here if you want more realistic food names
                $ingredient->setNom($faker->word());
                $ingredient->setQuantite($faker->numberBetween(10, 500) . 'g');
                
                // Link the ingredient to the current recipe
                $ingredient->setRecette($recette);
                
                $manager->persist($ingredient);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RecetteFixtures::class,
        ];
    }
}