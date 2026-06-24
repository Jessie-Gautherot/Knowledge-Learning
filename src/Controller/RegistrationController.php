<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Service\EmailService;

/**
 * Class RegistrationController
 *
 * Handles user registration and account activation.
 *
 * Responsibilities:
 * - Display registration form
 * - Validate and handle user input
 * - Hash password securely
 * - Generate activation token
 * - Persist user into database
 * - Activate account via token
 */
class RegistrationController extends AbstractController
{
    /**
     * Display and handle the registration form
     *
     * @param Request $request HTTP request
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     * @param UserPasswordHasherInterface $passwordHasher Password hasher service
     * @param EmailService $emailService Service used to send activation emails
     *
     * @return Response
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        EmailService $emailService
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get user password from form
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash the password before saving it
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainPassword
            );

            $user->setPassword($hashedPassword);

            // Generate activation token for email verification
            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            // Set user as not activated. Account must be activated via email
            $user->setIsActive(false);

            $entityManager->persist($user);
            $entityManager->flush();

            // Send account activation email 
            $emailService->sendActivationEmail(
                $user->getEmail(),
                $user->getActivationToken()
            );

            $this->addFlash(
                'success',
                'Votre compte utilisateur a bien été créé, veuillez consulter vos emails pour l\'activer.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Activate user account using token
     *
     * @param string $token Activation token sent by email
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     * @param UserRepository $userRepository Repository used to find the user
     *
     * @return Response
     */
    #[Route('/activate/{token}', name: 'app_activate')]
    public function activate(
        string $token,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {

        // Find user by activation token
        $user = $userRepository->findOneByActivationToken($token);

        if (!$user) {
            throw $this->createNotFoundException('token invalide.');
        }

        // Activate account and remove token
        $user->setIsActive(true);
        $user->setActivationToken(null);

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre compte est activé, vous pouvez vous connecter.'
        );

        return $this->redirectToRoute('app_login');
    }
}