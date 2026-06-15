<?php

namespace App\Tests\Purchase;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\CursusRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseFunctionalTest extends WebTestCase
{
    /**
    * Check that a logged in user can buy a cursus.
    *
    * This test checks that:
    * - the fixture user can access the payment success route
    * - a purchase is saved in database
    * - the purchase is created for correct user and cursus
    */
    public function testUserCanBuyCursus(): void
    {
        // Create a test client.
        $client = static::createClient();

        // Get services needed for the test.
        $entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $user = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'client@test.com'
            ]);

        $this->assertNotNull($user);
        $this->assertTrue($user->isActive());

        // Log in the user.
        $client->loginUser($user);

        // Get an existing cursus.
        $cursus = static::getContainer()
            ->get(CursusRepository::class)
            ->findOneBy([]);

        $this->assertNotNull($cursus);

        // Simulate a successful payment return.
        $client->request(
            'GET',
            '/purchase/cursus/' . $cursus->getId() . '/success'
        );

        $this->assertResponseRedirects();

        // Check that the purchase was created.
        $purchase = static::getContainer()
            ->get(PurchaseRepository::class)
            ->findOneBy([
                'user' => $user,
                'cursus' => $cursus
            ]);

        $this->assertNotNull($purchase);

        // Delete the purchase created during the test.
        $entityManager->remove($purchase);
        $entityManager->flush();
    }
}