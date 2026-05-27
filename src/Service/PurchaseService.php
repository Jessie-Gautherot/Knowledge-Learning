<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PurchaseService
 *
 * Handles all purchase logic of the application.
 * - Prevent duplicate purchases
 * - Create a purchase for a cursus or a lesson
 * - Check if a user has access to content
 * - Handle purchase business rules
 */
class PurchaseService
{
    private EntityManagerInterface $em;
    private PurchaseRepository $purchaseRepository;

    /**
     * Constructor
     */
    public function __construct(
        EntityManagerInterface $em,
        PurchaseRepository $purchaseRepository
    ) {
        $this->em = $em;
        $this->purchaseRepository = $purchaseRepository;
    }

    /**
    * Buy a cursus 
    *
    * - Checks if the user already bought the cursus
    * - Creates a new Purchase
    * - Saves the purchase in database
    *
    * @param User $user The user who buys the cursus
    * @param Cursus $cursus The cursus to buy
    *
    * @return void
    */
    public function buyCursus(User $user, Cursus $cursus): void
    {
        // Prevent duplicate purchase
        if ($this->purchaseRepository->hasBoughtCursus($user, $cursus)) {
            return;
        }

        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setType(Purchase::TYPE_CURSUS);
        $purchase->setCursus($cursus);
        $purchase->setPrice($cursus->getPrice());
        $purchase->setStatus(Purchase::STATUS_SUCCESS);

        $this->em->persist($purchase);
        $this->em->flush();
    }

    /**
    * Buy a lesson 
    * - Checks if the user already bought the lesson
    * - Creates a new Purchase 
    * - Saves the purchase in database
    *
    * @param User $user The user who buys the lesson
    * @param Lesson $lesson The lesson to buy
    *
    * @return void
    */
    public function buyLesson(User $user, Lesson $lesson): void
    {
        // Prevent duplicate purchase
        if ($this->purchaseRepository->hasBoughtLesson($user, $lesson)) {
            return;
        }

        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setType(Purchase::TYPE_LESSON);
        $purchase->setLesson($lesson);
        $purchase->setPrice($lesson->getPrice());
        $purchase->setStatus(Purchase::STATUS_SUCCESS);

        $this->em->persist($purchase);
        $this->em->flush();
    }

    /**
    * Check if a user bought a cursus.
    *
    * @param User $user
    * @param Cursus $cursus
    * @return bool
    */
    public function hasBoughtCursus(User $user, Cursus $cursus): bool
    {
        return $this->purchaseRepository->hasBoughtCursus($user, $cursus);
    }

    /**
    * Check if a user bought a lesson.
    *
    * @param User $user
    * @param Lesson $lesson
    * @return bool
    */
    public function hasBoughtLesson(User $user, Lesson $lesson): bool
    {
        return $this->purchaseRepository->hasBoughtLesson($user, $lesson);
    }

    /**
    * Check if a user can access a lesson.
    *
    * Access is allowed if:
    * - The user bought the lesson
    * - OR his parent cursus
    *
    * @param User $user The user
    * @param Lesson $lesson The lesson to check
    *
    * @return bool True if access is allowed, false otherwise
    */
    public function canAccessLesson(User $user, Lesson $lesson): bool
    {
        
        // Direct purchase a lesson
        if ($this->hasBoughtLesson($user, $lesson)) {
            return true;
        }

        // Access via cursus
        return $this->hasBoughtCursus($user, $lesson->getCursus());
        
    }
}