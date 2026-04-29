<?php

namespace App\Controller\Front;

use App\Repository\CursusRepository;
use App\Service\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CursusController
 *
 * Handles the display of cursus and related lessons for front users.
 */
class CursusController extends AbstractController
{
    /**
     * Display a cursus with its lessons.
     *
     * This method:
     * - Fetches cursus by ID
     * - Checks if the cursus exists
     * - Checks if the connected user has purchased the cursus
     * - Sends data to the Twig view
     *
     * @param int $id ID of the cursus to display
     * @param CursusRepository $cursusRepository Repository used to fetch cursus data
     * @param PurchaseService $purchaseService Service handling purchase logic
     *
     * @return Response The HTTP response rendering the cursus page
     */
    #[Route('/cursus/{id}', name: 'cursus_show')]
    public function show(
        int $id,
        CursusRepository $cursusRepository,
        PurchaseService $purchaseService
    ): Response {
        $cursus = $cursusRepository->find($id);

        if (!$cursus) {
            throw $this->createNotFoundException('Cursus not found');
        }

        $user = $this->getUser();

        $hasAccess = false;

        if ($user) {
            $hasAccess = $purchaseService->hasBoughtCursus($user, $cursus);
        }

        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
            'hasAccess' => $hasAccess,
        ]);
    }
}