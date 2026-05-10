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
            ['nom' => 'Entrée', 'icone' => '🥗', 'description' => 'Les meilleures entrées'],
            ['nom' => 'Plat', 'icone' => '🍝', 'description' => 'Plats principaux délicieux'],
            ['nom' => 'Dessert', 'icone' => '🍰', 'description' => 'Desserts sucrés'],
            ['nom' => 'Boisson', 'icone' => '🥤', 'description' => 'Boissons rafraîchissantes'],
            ['nom' => 'Snack', 'icone' => '🍕', 'description' => 'Snacks rapides'],
            ['nom' => 'Soupe', 'icone' => '🥣', 'description' => 'Soupes chaudes'],
        ];

        foreach ($categories as $i => $data) {
            $categorie = new CategorieRecette();
            $categorie->setNom($data['nom']);
            $categorie->setIcone($data['icone']);
            $categorie->setDescription($data['description']);

            $manager->persist($categorie);

            $this->addReference('categorie-' . $i, $categorie);
        }

        $manager->flush();
    }
}