<?php

namespace App\Controller\Front;

use App\Repository\LessonRepository;
use App\Service\PurchaseService;
use App\Service\LessonProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LessonController
 *
 * Handles display and validate lesson.
 */
class LessonController extends AbstractController
{
    /**
     * Display a lesson.
     *
     * This method:
     * - fetch a lesson by ID
     * - Checks if the lesson exists
     * - check if the user is authenticated
     * - Verifies if the user has access (purchase required)
     * - Renders the lesson view
     *
     * @param int $id The ID of the lesson to display
     *
     * @return Response The HTTP response rendering the lesson page
     */
    #[Route('/lesson/{id}', name: 'lesson_show')]
    public function show(
        int $id,
        LessonRepository $lessonRepository,
        PurchaseService $purchaseService
    ): Response {
        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'You must be logged in.');
            return $this->redirectToRoute('app_login');
        }

        if (!$purchaseService->canAccessLesson($user, $lesson)) {
            throw $this->createAccessDeniedException('Access denied to this lesson.');
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Validate a lesson.
     *
     * This method:
     * - Gets the lesson by ID
     * - Checks if the lesson exists
     * - Cheks if the user is authenticated
     * - Checks access rights (purchase required)
     * - Delegates validation logic to the LessonProgressService
     * - Redirects to the lesson page with a success message
     *
     * @param int $id The ID of the lesson to validate
     *
     * @return Response The HTTP redirect response after validation
     */
    #[Route('/lesson/{id}/validate', name: 'lesson_validate', methods: ['POST'])]
    public function validate(
        int $id,
        LessonRepository $lessonRepository,
        LessonProgressService $lessonProgressService,
        PurchaseService $purchaseService
    ): Response {
        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$purchaseService->canAccessLesson($user, $lesson)) {
        throw $this->createAccessDeniedException();
        }
 
        $lessonProgressService->validateLesson($user, $lesson);

        $this->addFlash('success', 'Lesson validated successfully.');

        return $this->redirectToRoute('lesson_show', ['id' => $id]);
    }
}