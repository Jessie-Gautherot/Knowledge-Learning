<?php

namespace App\Controller\Front;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Service\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PurchaseController
 *
 * Handles purchase actions (cursus / lesson).
 */
class PurchaseController extends AbstractController
{
    /**
    * Handles the purchase of a cursus.
    *
    * - Checks if the user is authenticated
    * - Verifies if the user account is activated
    * - Gets the cursus using its ID
    * - Calls PurchaseService to handle the purchase
    * - Redirect to the cursus page with a message
    *
    * @param int $id The ID of the cursus to purchase
    * @param CursusRepository $cursusRepository Repository used to retrieve the cursus
    * @param PurchaseService $purchaseService Service handling purchase logic
    *
    * @return Response HTTP redirect response
    */
    #[Route('/purchase/cursus/{id}', name: 'purchase_cursus')]
    public function buyCursus(
        int $id,
        CursusRepository $cursusRepository,
        PurchaseService $purchaseService
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->isActive()) {
            $this->addFlash('erreur', 'Votre compte n\'est pas activé.');
            return $this->redirectToRoute('cursus_show', ['id' => $id]);
        }

        $cursus = $cursusRepository->find($id);

        if (!$cursus) {
            throw $this->createNotFoundException('Cursus not found');
        }

        $purchaseService->buyCursus($user, $cursus);

        $this->addFlash('success', 'Cursus acheté avec succès.');

        return $this->redirectToRoute('cursus_show', ['id' => $id]);
    }

    /**
    * Handles the purchase of a lesson.
    *
    * - Checks if the user is authenticated
    * - Checks if the user account is activated
    * - Gets the lesson by its ID
    * - Calls PurchaseService to handle the purchase
    * - Redirects back to the lesson page with a message
    *
    * @param int $id The ID of the lesson to purchase
    * @param LessonRepository $lessonRepository Repository used to get the lesson
    * @param PurchaseService $purchaseService Service that manages purchases
    *
    * @return Response Redirect response to lesson page
    */
    #[Route('/purchase/lesson/{id}', name: 'purchase_lesson')]
    public function buyLesson(
        int $id,
        LessonRepository $lessonRepository,
        PurchaseService $purchaseService
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->isActive()) {
            $this->addFlash('erreur', 'Votre compte n\'est pas activé.');
            return $this->redirectToRoute('lesson_show', ['id' => $id]);
        }

        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }

        $purchaseService->buyLesson($user, $lesson);

        $this->addFlash('success', 'Lesson achétée avec succès.');

        return $this->redirectToRoute('lesson_show', ['id' => $id]);
    }
}