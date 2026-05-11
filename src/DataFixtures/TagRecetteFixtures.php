<?php

namespace App\DataFixtures;

use App\Entity\TagRecette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagRecetteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = [
            ['nom' => 'Végétarien', 'couleur' => '#4CAF50'],
            ['nom' => 'Végan', 'couleur' => '#8BC34A'],
            ['nom' => 'Sans Gluten', 'couleur' => '#FF9800'],
            ['nom' => 'Bio', 'couleur' => '#795548'],
            ['nom' => 'Rapide', 'couleur' => '#F44336'],
            ['nom' => 'Familial', 'couleur' => '#2196F3'],
            ['nom' => 'Festif', 'couleur' => '#9C27B0'],
            ['nom' => 'Économique', 'couleur' => '#FFEB3B'],
        ];

        foreach ($tags as $i => $t) {
            $tag = new TagRecette();
            $tag->setNom($t['nom']);
            $tag->setCouleur($t['couleur']);
            $manager->persist($tag);
            $this->addReference('tag_' . $i, $tag);
        }
        $manager->flush();
    }
}