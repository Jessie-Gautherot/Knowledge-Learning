<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Controller responsible for authentication (login & logout)
 *
 * Responsibilities:
 * - Display login form
 * - Show authentication errors
 * - Handle logout 
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
        // Get last authentication error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Get last entered username (email)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render login page
        return $this->render('Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
    * Handles user logout.
    *
    * Remove the authenticated user token
    * destroy the current session 
    * redirect user to the home page.
    *
    * @param Request $request
    * @param TokenStorageInterface $tokenStorage
    *
    * @return RedirectResponse
    */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(
        Request $request,
        TokenStorageInterface $tokenStorage
    ): RedirectResponse {
        $tokenStorage->setToken(null);

        $request->getSession()->invalidate();

        return $this->redirectToRoute('app_home');
    }
    }