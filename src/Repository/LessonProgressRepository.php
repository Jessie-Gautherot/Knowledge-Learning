<?php

namespace App\Repository;

use App\Entity\LessonProgress;
use App\Entity\User;
use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LessonProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonProgress::class);
    }

    /**
     * Check if a lesson is validated by user
     * 
     * Return true if :
     * - A lesson progress exist and the lesson is marked as validated
     *
     * @param User $user The user
     * @param Lesson $lesson The lesson
     *
     * @return bool True if validated
     */
    public function isLessonValidated(User $user, Lesson $lesson): bool
    {
        return (bool) $this->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
            'isValidated' => true
        ]);
    }

     /**
     * Find lesson progress for a given user and lesson
     *
     * - Returns the LessonProgress if it exist
     * - Returns null if no progress is found
     *
     * @param User $user The user
     * @param Lesson $lesson The lesson
     *
     * @return LessonProgress|null The progress entity or null
     */
    public function findOneProgress(User $user, Lesson $lesson): ?LessonProgress
    {
        return $this->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);
    }
}