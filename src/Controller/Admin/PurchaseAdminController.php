<?php

namespace App\Controller\Admin;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/purchases', name: 'admin_purchase_')]
#[IsGranted('ROLE_ADMIN')]
class PurchaseAdminController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        return $this->render('admin/purchase/index.html.twig', [
            'purchases' => $purchaseRepository->findAll(),
        ]);
    }
}