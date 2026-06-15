<?php

namespace App\Tests\Purchase;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Entity\User;
use App\Repository\PurchaseRepository;
use App\Service\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PurchaseServiceUnitTest extends TestCase
{
    /**
    * Check that a cursus purchase is correctly created.
    */
    public function testBuyCursus(): void
    {
        // Create test data.
        $user = new User();
       
        $cursus = new Cursus();
        $cursus->setTitle('Cursus test');
        $cursus->setPrice(5000);

        // Mock the repository for a not yet bought cursus
        $purchaseRepository = $this->createMock(PurchaseRepository::class);
        $purchaseRepository->method('hasBoughtCursus')
            ->willReturn(false);

        // Mock the EntityManager to check purchase creation.
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            // Check that the purchase contains the correct data.
            ->with($this->callback(function (Purchase $purchase) use ($user, $cursus) {
                return $purchase->getUser() === $user
                    && $purchase->getCursus() === $cursus
                    && $purchase->getType() === Purchase::TYPE_CURSUS
                    && $purchase->getPrice() === 5000
                    && $purchase->getStatus() === Purchase::STATUS_SUCCESS;
            }));

        // Check that flush is called.
        $entityManager->expects($this->once())
            ->method('flush');
        
        // Create the service with mocked dependencies.
        $purchaseService = new PurchaseService(
            $entityManager,
            $purchaseRepository
        );
        $purchaseService->buyCursus($user, $cursus);
    }

    /**
    * Check that a lesson purchase is correctly created.
    */
    public function testBuyLesson(): void
    {
        // Create test data.
        $user = new User();
        
        $lesson = new Lesson();
        $lesson->setTitle('Leçon test');
        $lesson->setContent('Contenu test');
        $lesson->setPrice(2600);

        // Mock the repository for a not yet bought lesson.
        $purchaseRepository = $this->createMock(PurchaseRepository::class);
        $purchaseRepository->method('hasBoughtLesson')
            ->willReturn(false);

        // Mock the EntityManager to check purchase creation.
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            // Check that the purchase contains the correct data.
            ->with($this->callback(function (Purchase $purchase) use ($user, $lesson) {
                return $purchase->getUser() === $user
                    && $purchase->getLesson() === $lesson
                    && $purchase->getType() === Purchase::TYPE_LESSON
                    && $purchase->getPrice() === 2600
                    && $purchase->getStatus() === Purchase::STATUS_SUCCESS;
            }));

        // Check that flush is called.
        $entityManager->expects($this->once())
            ->method('flush');
        
        // Create the service with mocked dependencies.
        $purchaseService = new PurchaseService(
            $entityManager,
            $purchaseRepository
        );
        $purchaseService->buyLesson($user, $lesson);
    }
}