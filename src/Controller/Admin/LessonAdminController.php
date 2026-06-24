<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Admin controller used to manage lessons.
 */
#[Route('/admin/lessons', name: 'admin_lesson_')]
#[IsGranted('ROLE_ADMIN')]
class LessonAdminController extends AbstractController
{
    /**
     * Display the list of lessons.
     */
    #[Route('', name: 'index')]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('admin/lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    /**
     * Create a new lesson.
     */
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lesson = new Lesson();

        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('admin_lesson_index');
        }

        return $this->render('admin/lesson/form.html.twig', [
            'form' => $form,
            'title' => 'Ajouter une leçon',
        ]);
    }

    /**
     * Updates an existing lesson.
     */
    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        Lesson $lesson,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_lesson_index');
        }

        return $this->render('admin/lesson/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier une leçon',
        ]);
    }

    /**
     * Deletes a lesson after CSRF validation.
     */
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        Lesson $lesson,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete_lesson_' . $lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_lesson_index');
    }
}