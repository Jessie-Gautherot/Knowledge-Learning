<?php

namespace App\Tests\RegisterLogin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActivationTest extends WebTestCase
{
    /**
     * Check that a user can activate his account with an activation token.
     *
     * This test check that:
     * - the user has an activation token
     * - the activation URL works
     * - the account becomes active
     * - the token is removed after activation
     */
    public function testUserActivation(): void
    {
        // Create a test client.
        $client = static::createClient();

        // Get services needed for the test.
        $entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        // Check if a test user already exist and delete it.
        $existingUser = $userRepository
            ->findOneByEmail('activation@test.com');
        if ($existingUser) {
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        // Create a not active test user.
        $user = new User();
        $user->setName('Activation Test');
        $user->setEmail('activation@test.com');
        $user->setPassword('fake-password');
        $user->setIsActive(false);
        // Token used to simulate activation link
        $user->setActivationToken('token-test');

        $entityManager->persist($user);
        $entityManager->flush();

        // Simulate click on activation link received by email
        $client->request(
            'GET',
            '/activate/token-test'
        );

        // Check that the user is redirected to login.
        $this->assertResponseRedirects('/login');

        // Reload the user and check his activation.
        $user = $userRepository
            ->findOneByEmail('activation@test.com');

        $this->assertTrue($user->isActive());
        $this->assertNull($user->getActivationToken());

        // Delete the user created during the test.
        $entityManager->remove($user);
        $entityManager->flush();
    }
}