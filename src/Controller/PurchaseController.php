<?php

namespace App\Controller;

use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Service\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    * - Check if the user is authenticated
    * - Check if the user account is activated
    * - Get the cursus 
    * - Create a Stripe checkout session
    * - Redirect the user to Stripe payment page
    *
    * @param int $id The ID of the cursus to purchase
    * @param CursusRepository $cursusRepository Repository used to get the cursus
    * @param StripeService $stripeService Service handling Stripe payments
    *
    * @return Response redirect response to Stripe checkout
    */
    #[Route('/purchase/cursus/{id}', name: 'purchase_cursus')]
    public function buyCursus(
        int $id,
        CursusRepository $cursusRepository,
        StripeService $stripeService
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

        $successUrl = $this->generateUrl('purchase_cursus_success', [
            'id' => $cursus->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $cancelUrl = $this->generateUrl('cursus_show', [
            'id' => $cursus->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $checkoutUrl = $stripeService->createCheckoutSession(
            $cursus->getTitle(),
            $cursus->getPrice(),
            $successUrl,
            $cancelUrl
        );

        return new RedirectResponse($checkoutUrl);
        }

    /**
    * Handles successful cursus payment.
    *
    * - Check if the user is authenticated
    * - Get the purchased cursus
    * - Create the purchase in database
    * - Redirect with a success message
    *
    * @return Response Redirect response
    */
    #[Route('/purchase/cursus/{id}/success', name: 'purchase_cursus_success')]
    public function successCursus(
    int $id,
        CursusRepository $cursusRepository,
        PurchaseService $purchaseService
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
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
    * - Check if the user is authenticated
    * - Check if the user account is activated
    * - Get the lesson by its ID
    * - Create a Stripe checkout session
    * - Redirect the user to Stripe payment page
    *
    * @param int $id The ID of the lesson to purchase
    * @param LessonRepository $lessonRepository Repository used to get the lesson
    * @param StripeService $stripeService Service handling Stripe payments
    *
    * @return Response Redirect response to Stripe checkout
    */
    #[Route('/purchase/lesson/{id}', name: 'purchase_lesson')]
    public function buyLesson(
        int $id,
        LessonRepository $lessonRepository,
        StripeService $stripeService
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }

        if (!$user->isActive()) {
            $this->addFlash('erreur', 'Votre compte n\'est pas activé.');

            return $this->redirectToRoute('cursus_show', [
                'id' => $lesson->getCursus()->getId(),
            ]);
        }

        $successUrl = $this->generateUrl('purchase_lesson_success', [
            'id' => $lesson->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $cancelUrl = $this->generateUrl('cursus_show', [
            'id' => $lesson->getCursus()->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $checkoutUrl = $stripeService->createCheckoutSession(
            $lesson->getTitle(),
            $lesson->getPrice(),
            $successUrl,
            $cancelUrl
        );

        return new RedirectResponse($checkoutUrl);
    }

    /**
    * Handles successful lesson payment.
    *
    * - Check if the user is authenticated
    * - Get the lesson by its ID
    * - Create the purchase in database
    * - Redirect with a success message
    *
    * @return Response Redirect response
    */
    #[Route('/purchase/lesson/{id}/success', name: 'purchase_lesson_success')]
    public function successLesson(
        int $id,
        LessonRepository $lessonRepository,
        PurchaseService $purchaseService
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }

        $purchaseService->buyLesson($user, $lesson);

        $this->addFlash('success', 'Leçon achetée avec succès.');

        return $this->redirectToRoute('lesson_show', ['id' => $id]);
    }
}