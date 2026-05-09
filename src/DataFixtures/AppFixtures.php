<?php

namespace App\DataFixtures;

use App\Entity\CategorieRecette;
use App\Entity\Recette;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $categorie = new CategorieRecette();
        $categorie->setNom('Dessert');
        $categorie->setIcone('🍰');
        $categorie->setDescription('Recettes sucrées pour finir le repas en beauté');
        $manager->persist($categorie);
        $this->addReference('cat_dessert', $categorie);

        $user = new User();
        $user->setEmail('chef@recipehub.com');
        $user->setPseudo('ChefTest');
        $user->setRoles(['ROLE_CUISINIER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'chef123'));
        $manager->persist($user);
        $this->addReference('user_chef', $user);

        $recette = new Recette();
        $recette->setTitre('Tarte aux pommes');
        $recette->setDescription('Une délicieuse tarte aux pommes maison');
        $recette->setInstructions('Éplucher les pommes, préparer la pâte, cuire au four');
        $recette->setTempsPreparation(30);
        $recette->setTempsCuisson(45);
        $recette->setDifficulte('facile');
        $recette->setNbPersonnes(6);
        $recette->setPubliee(true);
        $recette->setCategorie($categorie);
        $recette->setAuteur($user);
        $manager->persist($recette);

        $manager->flush();
    }
}
