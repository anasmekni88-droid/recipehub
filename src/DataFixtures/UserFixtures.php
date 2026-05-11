<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $admin = new User();
        $admin->setEmail('admin@recipehub.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $admin->setPseudo('Admin');
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Chef
        $chef = new User();
        $chef->setEmail('chef@recipehub.com');
        $chef->setRoles(['ROLE_CUISINIER']);
        $chef->setPassword($this->hasher->hashPassword($chef, 'chef123'));
        $chef->setPseudo('Chef Master');
        $manager->persist($chef);
        $this->addReference('user_chef', $chef);

        // 5 Utilisateurs normaux
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'user123'));
            $user->setPseudo($faker->userName());
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}