<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class UserAuthenticator
 *
 * Handles user authentication.
 *
 * Responsibilities:
 * - Get login form data
 * - Find user by email
 * - Check password
 * - Redirect the user after login
 * - Handle authentication errors
 * - Protect the login form with a CSRF token
 */
class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private RouterInterface $router;
    private UserRepository $userRepository;

    /**
     * Constructor
     *
     * @param RouterInterface $router Symfony router service
     * @param UserRepository $userRepository User repository
     */
    public function __construct(
        RouterInterface $router,
        UserRepository $userRepository
    ) {
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    /**
     * Handles login form submission
     *
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        // Get login data from the form
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        // Save the last entered email
        $request->getSession()->set(
            SecurityRequestAttributes::LAST_USERNAME,
            $email
        );

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {

                // Find user by email
                $user = $this->userRepository->findOneByEmail($userIdentifier);

                // User not found
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException(
                        'Email invalide.'
                    );
                }

                return $user;
            }),

            // Symfony checks the password automatically
            new PasswordCredentials($password),

            [
                // Protect the form against CSRF attacks
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    /**
     * Called after a successful login.
     *
     * @param Request $request
     * @param mixed $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(
        Request $request,
        $token,
        string $firewallName
    ): ?Response {

        // Redirect to the requested page if available
        if ($targetPath = $this->getTargetPath(
            $request->getSession(),
            $firewallName
        )) {
            return new RedirectResponse($targetPath);
        }

        // Default redirection (home page)
        return new RedirectResponse(
        $this->router->generate('app_home')
        );
    }

    /**
     * Called after a failed login.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): Response {

        // Save error message in session
        $request->getSession()->set(
            SecurityRequestAttributes::AUTHENTICATION_ERROR,
            $exception
        );

        return new RedirectResponse(
        $this->getLoginUrl($request)
        );
    }

    /**
     * Returns the login page URL.
     *
     * @param Request $request
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}