<?php

namespace App\Tests\Data;

use App\Entity\Lesson;
use App\Entity\LessonProgress;
use App\Entity\User;
use App\Repository\LessonProgressRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests LessonProgressRepository custom methods.
 *
 * Checks lesson validation
 * and finding lesson progress.
 */
class LessonProgressRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private LessonProgressRepository $lessonProgressRepository;

    private User $user;

    private Lesson $lesson;

    // Progress created during tests
    private ?LessonProgress $createdProgression = null;

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
        $this->lessonProgressRepository = $container->get(LessonProgressRepository::class);

        // Get a fixture user.
        $this->user = $container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'client@test.com'
            ]);

        // Get an existing lesson.
        $lessonRepository = $container->get(LessonRepository::class);
        $this->lesson = $lessonRepository->findOneBy([]);

        $this->assertNotNull($this->user);
        $this->assertNotNull($this->lesson);
    }

    /**
     * Delete the progress created during tests.
     */
    protected function tearDown(): void
    {
        if (
            $this->createdProgression !== null
            && $this->entityManager->contains($this->createdProgression)
        ) {
            $this->entityManager->remove($this->createdProgression);
            $this->entityManager->flush();
        }

        $this->createdProgression = null;

        parent::tearDown();
    }

    /**
    * Utility method to create a progress
    */
    private function createLessonProgress(bool $isValidated): LessonProgress
    {
        $progress = new LessonProgress();

        $progress->setUser($this->user);
        $progress->setLesson($this->lesson);
        $progress->setIsValidated($isValidated);

        $this->entityManager->persist($progress);
        $this->entityManager->flush();

        $this->createdProgression = $progress;

        return $progress;
    }

    /**
     * Check that a validated lesson is detected for the user.
     */
    public function testIsLessonValidated(): void
    {
        $this->createLessonProgress(true);

        $this->assertTrue(
            $this->lessonProgressRepository->isLessonValidated(
                $this->user,
                $this->lesson
            )
        );
    }

    /**
     * Check that a not validated lesson returns false.
     */
    public function testIsLessonValidatedReturnsFalse(): void
    {
        $this->assertFalse(
            $this->lessonProgressRepository->isLessonValidated(
                $this->user,
                $this->lesson
            )
        );
    }

    /**
     * Check that an existing progress can be found.
     */
    public function testFindOneProgress(): void
    {
        $this->createLessonProgress(true);

        $foundProgress = $this->lessonProgressRepository->findOneProgress(
            $this->user,
            $this->lesson
        );

        $this->assertNotNull($foundProgress);

        // Check that the lesson matches.
        $this->assertSame(
            $this->lesson,
            $foundProgress->getLesson()
        );
    }

    /**
     * Check that null is returned when no progress exists.
     */
    public function testFindOneProgressReturnsNull(): void
    {
        // Get a missing progress.
        $progress = $this->lessonProgressRepository->findOneProgress(
            $this->user,
            $this->lesson
        );

        $this->assertNull($progress);
    }
}