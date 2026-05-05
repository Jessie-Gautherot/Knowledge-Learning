<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Lesson;
use App\Entity\Cursus;
use App\Entity\Theme;
use App\Entity\LessonProgress;
use App\Repository\LessonProgressRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LessonProgressService
 *
 * Handles lesson validation and theme completion check.
 *
 * Responsibilities:
 * - Validate a lesson for a user
 * - Save lesson progress in database
 * - Check if a cursus is fully completed 
 * - Check if a theme is fully completed for certification
 * - Call certificationService if needed
 */
class LessonProgressService
{
    /**
     * Constructor
     */
    public function __construct(
        private EntityManagerInterface $em,
        private LessonProgressRepository $repo,
        private CertificationService $certificationService
    ) {}

    /**
     * Validate a lesson for a given user
     */
    public function validateLesson(User $user, Lesson $lesson): void
    {
        // Get existing progress for this user and lesson
        $progress = $this->repo->findOneProgress($user, $lesson);

        // Avoid duplicate validation
        if ($progress && $progress->isValidated()) {
            return;
        }

        // Create progress if it does not exist
        if (!$progress) {
            $progress = new LessonProgress();
            $progress->setUser($user);
            $progress->setLesson($lesson);
        }

        // Mark lesson as validated
        $progress->setIsValidated(true);

        // Save lesson progress in database
        $this->em->persist($progress);
        $this->em->flush();

        // Check if the theme can be certified after this lesson validation
        $this->validateThemeIfCompleted($user, $lesson->getCursus()->getTheme());
    }

    /**
     * Check if a cursus is fully validated for a user
     */
    public function isCursusValidated(User $user, Cursus $cursus): bool
    {
        foreach ($cursus->getLessons() as $lesson) {
            if (!$this->repo->isLessonValidated($user, $lesson)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if all lessons of a theme are validated
     * If yes, create certification
     */
    private function validateThemeIfCompleted(User $user, Theme $theme): void
    {
        foreach ($theme->getCursus() as $cursus) {
            foreach ($cursus->getLessons() as $lesson) {
                if (!$this->repo->isLessonValidated($user, $lesson)) {
                    return; // Stop if one lesson is missing
                }
            }
        }

        // All lessons of all cursus are validated
        $this->certificationService->createIfNotExists($user, $theme);
    }
}