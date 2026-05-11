<?php

namespace App\DataFixtures;

use App\Entity\CategorieRecette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieRecetteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['nom' => 'Entrée', 'icone' => '🥗'],
            ['nom' => 'Plat', 'icone' => '🍝'],
            ['nom' => 'Dessert', 'icone' => '🍰'],
            ['nom' => 'Boisson', 'icone' => '🥤'],
            ['nom' => 'Snack', 'icone' => '🍕'],
            ['nom' => 'Soupe', 'icone' => '🥣'],
        ];

        foreach ($categories as $i => $cat) {
            $categorie = new CategorieRecette();
            $categorie->setNom($cat['nom']);
            $categorie->setIcone($cat['icone']);
            $manager->persist($categorie);
            
            // On sauvegarde une référence pour lier aux recettes plus tard
            $this->addReference('cat_' . $i, $categorie); 
        }
        $manager->flush();
    }
}