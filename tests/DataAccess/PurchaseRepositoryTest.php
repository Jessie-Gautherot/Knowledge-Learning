<?php

namespace App\Tests\Data;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Entity\User;
use App\Repository\CursusRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests for PurchaseRepository.
 *
 * - find purchases by user
 * - check cursus purchases
 * - check lesson purchases
 * - handle purchase status correctly
 */
class PurchaseRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private PurchaseRepository $purchaseRepository;

    private CursusRepository $cursusRepository;

    // Fixture user used in tests.
    private User $user;

    // Purchases created during tests.
    private array $createdPurchases = [];

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
        $this->purchaseRepository = $container->get(PurchaseRepository::class);
        $this->cursusRepository = $container->get(CursusRepository::class);

        // Get a fixture user.
        $this->user = $container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'client@test.com'
            ]);

        $this->assertNotNull($this->user);
    }

    /**
    * Delete purchases created during tests.
    */
    protected function tearDown(): void
    {
        foreach ($this->createdPurchases as $purchase) {
           if ($this->entityManager->contains($purchase)) {
               $this->entityManager->remove($purchase);
            }
        }
        $this->entityManager->flush();
        // Reset the purchase list.
        $this->createdPurchases = [];

        parent::tearDown();
    }

    /**
    * Get an existing cursus.
    */
    private function getCursus(): Cursus 
    {
        $cursus = $this->cursusRepository->findOneBy([]);

        $this->assertNotNull($cursus);

        return $cursus;
    }

     /**
    * Get a cursus with at least one lesson.
    */
    private function getCursusWithLesson(): Cursus
    {
        foreach ($this->cursusRepository->findAll() as $cursus) {
            if (!$cursus->getLessons()->isEmpty()) {
                return $cursus;
            }
        }

        $this->fail('Aucun cursus avec leçon trouvé dans les fixtures.');
    }

    /**
    * Create a purchase for the test user.
    * The purchase can be for a cursus or a lesson.
    */
    private function createPurchase(
        string $type,
        string $status,
        ?Cursus $cursus = null, 
        ?Lesson $lesson = null
    ): Purchase {

        $purchase = new Purchase();
        // Set common purchase data.
        $purchase->setUser($this->user);
        $purchase->setType($type);
        $purchase->setStatus($status);

        // Handle cursus purchase.
        if ($cursus !== null) {
            $purchase->setCursus($cursus);
            $purchase->setPrice($cursus->getPrice());
        }
        // Handle lesson purchase.
        if ($lesson !== null) {
            $purchase->setLesson($lesson);
            $purchase->setPrice($lesson->getPrice());
        }
        // Save the purchase.
        $this->entityManager->persist($purchase);
        $this->entityManager->flush();

        // Keep the purchase for cleanup.
        $this->createdPurchases[] = $purchase;
       
        return $purchase;
    }

    /**
    * Check that a valid cursus purchase is found.
    */
    public function testHasBoughtCursus(): void
    {
        // Get an existing cursus in fixture
        $cursus = $this->getCursus();

        // Create a successful cursus purchase.
        $this->createPurchase(
            Purchase::TYPE_CURSUS,
            Purchase::STATUS_SUCCESS,
            $cursus
        );

        // The method must return true for this valid purchase.
        $this->assertTrue(
            $this->purchaseRepository->hasBoughtCursus(
                $this->user,
                $cursus
            )
        );
    }

    /**
    * Check that a failed cursus purchase returns false.
    */
    public function testFailedCursusPurchaseReturnsFalse(): void
    {
        // Get an existing cursus in fixture
        $cursus = $this->getCursus();

        // Create a failed cursus purchase.
        $this->createPurchase(
            Purchase::TYPE_CURSUS,
            Purchase::STATUS_FAILED,
            $cursus
        );

        // The method must return false for this invalid purchase.
        $this->assertFalse(
            $this->purchaseRepository->hasBoughtCursus(
                $this->user,
                $cursus
            )
        );
    }

    /**
    * Check that a valid lesson purchase is found.
    */
    public function testHasBoughtLesson(): void
    {
        // Get a cursus with at least one lesson
        $cursus = $this->getCursusWithLesson();

        // Get the first lesson. 
        $lesson = $cursus->getLessons()->first();

        // Create a successful lesson purchase.
        $this->createPurchase(
            Purchase::TYPE_LESSON,
            Purchase::STATUS_SUCCESS,
            null,
            $lesson
        );

        // The method must return true for this valid lesson purchase.
        $this->assertTrue(
            $this->purchaseRepository->hasBoughtLesson(
                $this->user,
                $lesson
            )
        );
    }

   /**
    * Check that user purchases can be found.
    */
    public function testFindByUser(): void
    {
        // Get an existing cursus in fixture. 
        $cursus = $this->getCursus();

        // Create a purchase.
        $purchase = $this->createPurchase(
            Purchase::TYPE_CURSUS,
            Purchase::STATUS_SUCCESS,
            $cursus
        );

        // Get user purchases.
        $purchases = $this->purchaseRepository->findByUser($this->user);

        // Check that the result is an array.
        $this->assertIsArray($purchases);

        // Check that the purchase is in the result.
            $this->assertContains(
                $purchase,
                $purchases
            );
    }
}