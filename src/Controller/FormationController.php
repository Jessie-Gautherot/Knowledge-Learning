<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormationController
 *
 * Displays all formations 
 */
class FormationController extends AbstractController
{
    /**
     * Display all themes for formation page
     *
     * @param ThemeRepository $themeRepository
     * @return Response
     */
    #[Route('/formations', name: 'app_formations')]
    public function index(ThemeRepository $themeRepository): Response
    {
        return $this->render('Training/index.html.twig', [
            'themes' => $themeRepository->findAll(),
        ]);
    }
}