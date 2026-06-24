<?php

namespace App\Tests\Data;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * Tests for UserRepository.
 *
 * Check user search methods used for:
 * - authentication by email
 * - account activation by token
 * - unknown user handling
 */
class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;

    // Users created during tests, will be delete in tearDown().
    private array $createdUsers = [];

    /**
     * Set up common data for all tests.
     */
    protected function setUp(): void
    {
        // Start Symfony to get services.
        self::bootKernel();

        // Get the Symfony service container.
        $container = static::getContainer();

        // Get services needed for tests.
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
    }

    /**
    * Delete users created during tests.
    */
    protected function tearDown(): void
    {
        foreach ($this->createdUsers as $user) {
            if ($this->entityManager->contains($user)) {
                $this->entityManager->remove($user);
            }
        }

        $this->entityManager->flush();

        // Reset the user list.
        $this->createdUsers = [];

        parent::tearDown();
    }

    /**
     * Create a user with an activation token.
     */
    private function createUserWithActivationToken(): User
    {
        $user = new User();
        $user->setName('Token Test');
        $user->setEmail('token@test.com');
        $user->setPassword('fake-password');
        // Set the user as not active.
        $user->setIsActive(false);
        // Token used for activation test
        $user->setActivationToken('token-test-repository');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Keep the user for cleanup.
        $this->createdUsers[] = $user;

        return $user;
    }

    /**
     * Check that an existing user (fixture) can be found by email.
     */
    public function testFindOneByEmail(): void
    {
        $user = $this->userRepository->findOneByEmail(
            'client@test.com'
        );
        $this->assertNotNull($user);
    }

    /**
     * Check that an unknown email returns null.
     */
    public function testFindOneByEmailReturnsNull(): void
    {
        $user = $this->userRepository->findOneByEmail(
            'inconnu@test.com'
        );
        $this->assertNull($user);
    }

    /**
     * Check that Symfony can load a user for authentication.
     */
    public function testLoadUserByIdentifier(): void
    {
        // load a fixture user by email.
        $user = $this->userRepository->loadUserByIdentifier(
            'client@test.com'
        );

        // Check that the loaded user is correct.
        $this->assertEquals(
            'client@test.com',
            $user->getEmail()
        );
    }

    /**
     * Check that an exception is thrown for an unknown user.
     */
    public function testLoadUserByIdentifierThrowsException(): void
    {
        // An exception is expected.
        $this->expectException(
            UserNotFoundException::class
        );

        // Try to load an unknown user.
        $this->userRepository->loadUserByIdentifier(
            'inconnu@test.com'
        );
    }

    /**
     * Check that a user can be found by activation token.
     */
    public function testFindOneByActivationToken(): void
    {
        // Create a user with an activation token.
        $this->createUserWithActivationToken();

        // Get the user by activation token.
        $foundUser = $this->userRepository
            ->findOneByActivationToken(
                'token-test-repository'
            );
        $this->assertNotNull($foundUser);

        // Check that the found user is correct.
        $this->assertEquals(
            'token@test.com',
            $foundUser->getEmail()
        );
    }

    /**
     * Check that an invalid token returns null.
     */
    public function testFindOneByActivationTokenReturnsNull(): void
    {
        // Try to get a user by an invalid token.
        $user = $this->userRepository->findOneByActivationToken(
            'invalid-token'
        );
        // Check that no user is found.
        $this->assertNull($user);
    }
}