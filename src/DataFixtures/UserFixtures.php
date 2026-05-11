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
        $faker = Factory::create();

        // ADMIN
        $admin = new User();
        $admin->setEmail('admin@recipehub.com');
        $admin->setPseudo('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);
        $this->addReference('user-admin', $admin);

        // CHEF
        $chef = new User();
        $chef->setEmail('chef@recipehub.com');
        $chef->setPseudo('chef');
        $chef->setRoles(['ROLE_CUISINIER']);
        $chef->setPassword($this->hasher->hashPassword($chef, 'chef123'));
        $manager->persist($chef);
        $this->addReference('user-chef', $chef);

        // USERS (IMPORTANT: unique emails ONLY)
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();

            $user->setEmail($faker->unique()->safeEmail());
            $user->setPseudo($faker->userName());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
        }

        $manager->flush();
    }
}