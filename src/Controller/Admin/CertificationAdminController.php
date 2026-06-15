<?php

namespace App\Controller\Admin;

use App\Repository\CertificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/certifications', name: 'admin_certification_')]
#[IsGranted('ROLE_ADMIN')]
class CertificationAdminController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(CertificationRepository $certificationRepository): Response
    {
        return $this->render('admin/certification/index.html.twig', [
            'certifications' => $certificationRepository->findAll(),
        ]);
    }
}