<?php

namespace App\Repository;

use App\Entity\Purchase;
use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PurchaseRepository
 *
 * Handles data access for Purchase entity.
 */
class PurchaseRepository extends ServiceEntityRepository
{
    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    /**
     * Find all purchases for a user
     *
     * @param User $user
     * @return Purchase[]
     */
    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    /**
     * Check if user bought a cursus (success only)
     *
     * @param User $user
     * @param Cursus $cursus
     * @return bool
     */
    public function hasBoughtCursus(User $user, Cursus $cursus): bool
    {
        return (bool) $this->findOneBy([
            'user' => $user,
            'cursus' => $cursus,
            'type' => Purchase::TYPE_CURSUS,
            'status' => Purchase::STATUS_SUCCESS
        ]);
    }

    /**
     * Check if user bought a lesson (success only)
     *
     * @param User $user
     * @param Lesson $lesson
     * @return bool
     */
    public function hasBoughtLesson(User $user, Lesson $lesson): bool
    {
        return (bool) $this->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
            'type' => Purchase::TYPE_LESSON,
            'status' => Purchase::STATUS_SUCCESS
        ]);
    }
}