<?php

namespace App\Controller\Admin;

use App\Entity\Cursus;
use App\Form\CursusType;
use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Admin controller used to manage cursus.
 */
#[Route('/admin/cursus', name: 'admin_cursus_')]
#[IsGranted('ROLE_ADMIN')]
class CursusAdminController extends AbstractController
{
    /**
     * Displays the list of all cursus.
     */
    #[Route('', name: 'index')]
    public function index(CursusRepository $cursusRepository): Response
    {
        return $this->render('admin/cursus/index.html.twig', [
            'cursusList' => $cursusRepository->findAll(),
        ]);
    }

    /**
     * Create a new cursus.
     */
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cursus = new Cursus();

        $form = $this->createForm(CursusType::class, $cursus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursus);
            $entityManager->flush();

            return $this->redirectToRoute('admin_cursus_index');
        }

        return $this->render('admin/cursus/form.html.twig', [
            'form' => $form,
            'title' => 'Ajouter un cursus',
        ]);
    }

    /**
     * Update an existing cursus.
     */
    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        Cursus $cursus,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CursusType::class, $cursus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_cursus_index');
        }

        return $this->render('admin/cursus/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier un cursus',
        ]);
    }

    /**
     * Delete a cursus after CSRF validation.
     */
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        Cursus $cursus,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete_cursus_' . $cursus->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cursus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_cursus_index');
    }
}