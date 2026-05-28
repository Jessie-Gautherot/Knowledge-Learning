<?php

namespace App\Controller;

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
     * - Get a lesson by ID
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
        PurchaseService $purchaseService,
        LessonProgressService $lessonProgressService
    ): Response {
        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Leçon introuvable');
        }

        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('erreur', 'Vous n\'êtes pas connecté.');
            return $this->redirectToRoute('app_login');
        }

        if (!$purchaseService->canAccessLesson($user, $lesson)) {
            throw $this->createAccessDeniedException('Accès refusé à cette leçon.');
        }

        $isValidated = $lessonProgressService->isLessonValidated(
            $user,
            $lesson
        );

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'isValidated' => $isValidated,
        ]);
    }

    /**
     * Validate a lesson.
     *
     * This method:
     * - Gets the lesson by ID
     * - Checks if the lesson exists
     * - Checks if the user is authenticated
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
            throw $this->createNotFoundException('Leçon non trouvée');
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$purchaseService->canAccessLesson($user, $lesson)) {
        throw $this->createAccessDeniedException('Accès refusé à cette leçon.');
        }
 
        $lessonProgressService->validateLesson($user, $lesson);

        $this->addFlash('success', 'Leçon validée avec succès.');

        return $this->redirectToRoute('lesson_show', ['id' => $id]);
    }
}