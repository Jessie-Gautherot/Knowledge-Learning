<?php

namespace App\Controller;

use App\Repository\CertificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CertificationController
 *
 * Handles display of user certifications.
 */
class CertificationController extends AbstractController
{
    /**
     * Display certifications obtained by the user.
     *
     * This method:
     * - Check if user is authenticated
     * - Get certifications of the connected user
     * - Send data to Twig view
     *
     * @param CertificationRepository $certificationRepository
     *
     * @return Response The HTTP response rendering certifications page
     */
    #[Route('/certifications', name: 'certification_index')]
    public function index(
        CertificationRepository $certificationRepository
    ): Response {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $certifications = $certificationRepository->findBy([
            'user' => $user
        ]);

        return $this->render('certification/index.html.twig', [
            'certifications' => $certifications,
        ]);
    }
}