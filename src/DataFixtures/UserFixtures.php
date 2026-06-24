<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures
 *
 * Loads users into database.
 *
 * This fixture creates:
 * - One active client user
 * - One active administrator user
 */
class UserFixtures extends Fixture
{
    /**
     * Constructor
     *
     * @param UserPasswordHasherInterface $passwordHasher Service used to hash passwords
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Load users into database
     *
     * @param ObjectManager $manager Doctrine entity manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Create active client user
        $user = new User();

        $user->setName('Client User');
        $user->setEmail('client@test.com');

        // Hash password before saving
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'Password1'
        );

        $user->setPassword($hashedPassword);
        $user->setIsActive(true);
        $user->setActivationToken(null);

        $manager->persist($user);

        // Create administrator user
        $admin = new User();

        $admin->setName('Admin User');
        $admin->setEmail('admin@test.com');

        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'AdminPass1'
        );

        $admin->setPassword($hashedPassword);

        // Assign administrator role
        $admin->setRoles(['ROLE_ADMIN']);

        $admin->setIsActive(true);
        $admin->setActivationToken(null);

        $manager->persist($admin);

        // Save all users
        $manager->flush();
    }
}