<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use App\Entity\CategorieRecette;
use App\Entity\User;
use App\Entity\TagRecette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class RecetteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $difficultes = ['facile', 'moyen', 'difficile'];
        $authors = ['user_admin', 'user_chef', 'user_0', 'user_1', 'user_2', 'user_3', 'user_4'];

        for ($i = 0; $i < 20; $i++) {
            $recette = new Recette();
            $recette->setTitre($faker->sentence(4)); 
            $recette->setDescription(str_pad($faker->paragraph(2), 35, ' ')); 
            $recette->setInstructions($faker->text(300));
            $recette->setTempsPreparation($faker->numberBetween(10, 60));
            $recette->setTempsCuisson($faker->numberBetween(0, 120));
            $recette->setDifficulte($faker->randomElement($difficultes));
            $recette->setNbPersonnes($faker->numberBetween(1, 10));
            
            $date = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now'));
            $recette->setDateCreation($date);
            $recette->setPubliee($faker->boolean(80));

            // CORRECTION ICI : d'abord le nom ('cat_X'), ENSUITE la classe
            $recette->setCategorie($this->getReference('cat_' . $faker->numberBetween(0, 5), CategorieRecette::class));
            $recette->setAuteur($this->getReference($faker->randomElement($authors), User::class));

            $nbTags = $faker->numberBetween(1, 4);
            $tagsSelectionnes = (array) array_rand(range(0, 7), $nbTags);
            foreach ($tagsSelectionnes as $indexTag) {
                // CORRECTION ICI : d'abord le nom, ENSUITE la classe
                $recette->addTag($this->getReference('tag_' . $indexTag, TagRecette::class));
            }

            $manager->persist($recette);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategorieRecetteFixtures::class,
            TagRecetteFixtures::class,
            UserFixtures::class,
        ];
    }
}