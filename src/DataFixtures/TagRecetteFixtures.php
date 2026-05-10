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
            ['nom' => 'Végan', 'couleur' => '#2E7D32'],
            ['nom' => 'Sans Gluten', 'couleur' => '#2196F3'],
            ['nom' => 'Bio', 'couleur' => '#8D6E63'],
            ['nom' => 'Rapide', 'couleur' => '#FF9800'],
            ['nom' => 'Familial', 'couleur' => '#9C27B0'],
            ['nom' => 'Festif', 'couleur' => '#E91E63'],
            ['nom' => 'Économique', 'couleur' => '#607D8B'],
        ];

        foreach ($tags as $i => $data) {
            $tag = new TagRecette();
            $tag->setNom($data['nom']);
            $tag->setCouleur($data['couleur']);

            $manager->persist($tag);

            $this->addReference('tag-' . $i, $tag);
        }

        $manager->flush();
    }
}