<?php

namespace App\Tests\RegisterLogin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase; 

class RegistrationTest extends WebTestCase 
{
    /**
    * Check that a user can register.
    *
    * This test checks that:
    * - the registration form works
    * - the user is saved in database
    * - the account is created as inactive
    * - an activation token is generated
    */
    public function testUserRegistration(): void
    {
        // Create a test client.
        $client = static::createClient(); 

        // Get services needed for the test.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        // Check if a test user already exists and delete it.
        $existingUser = $userRepository->findOneByEmail('test@test.com');
        if ($existingUser) {
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        // Open the registration page.
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        // Complete and submit the registration form.
        $form = $crawler->filter('form')->form([
            'registration_form[name]' => 'Test User',
            'registration_form[email]' => 'test@test.com',
            'registration_form[plainPassword][first]' => 'Password1',
            'registration_form[plainPassword][second]' => 'Password1',
        ]);
        $client->submit($form);

        // Check the redirect to the login page.
        $this->assertResponseRedirects('/login');

        // Check the created account information.
        $user = $userRepository->findOneByEmail('test@test.com');

        $this->assertNotNull($user);
        $this->assertFalse($user->isActive());
        $this->assertNotNull($user->getActivationToken());

        // Delete the user created during the test.
        $entityManager->remove($user);
        $entityManager->flush();
    }
}