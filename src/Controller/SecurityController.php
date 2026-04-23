<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller responsible for authentication (login & logout)
 *
 * Responsibilities:
 * - Display login form
 * - Show authentication errors
 * - Handle logout (intercepted by Symfony)
 */
class SecurityController extends AbstractController
{
    /**
     * Display login page and handle authentication errors
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        /**
         * Get last authentication error (if any)
         */
        $error = $authenticationUtils->getLastAuthenticationError();

        /**
         * Get last entered username (email)
         */
        $lastUsername = $authenticationUtils->getLastUsername();

        /**
         * Render login page
         */
        return $this->render('Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Logout route (handled automatically by Symfony firewall)
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Méthode vide interceptée par le firewall.');
    }
}