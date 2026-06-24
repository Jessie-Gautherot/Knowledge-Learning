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
 * - Handle user logout 
 */
class SecurityController extends AbstractController
{
    /**
     * Display login page and handle authentication errors
     *
     * @param AuthenticationUtils $authenticationUtils Helper used to retrieve login errors and last username
     * 
     * @return Response Login page response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
    * Handles user logout.
    *
    * Remove the authenticated user token
    * Invalidate the current session 
    * Redirect the user to the home page.
    *
    * @param Request $request Current HTTP request
    * @param TokenStorageInterface $tokenStorage Authentication token storage
    *
    * @return RedirectResponse Redirect response to home page
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