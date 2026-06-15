<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/themes', name: 'admin_theme_')]
#[IsGranted('ROLE_ADMIN')]
class ThemeAdminController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(ThemeRepository $themeRepository): Response
    {
        return $this->render('admin/theme/index.html.twig', [
            'themes' => $themeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('admin_theme_index');
        }

        return $this->render('admin/theme/form.html.twig', [
            'form' => $form,
            'title' => 'Ajouter un thème',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        Theme $theme,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_theme_index');
        }

        return $this->render('admin/theme/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier un thème',
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        Theme $theme,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete_theme_' . $theme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($theme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_theme_index');
    }
}