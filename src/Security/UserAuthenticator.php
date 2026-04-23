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
 * Handles user authentication (login process).
 *
 * Responsibilities:
 * - Retrieve login credentials
 * - Load user from database
 * - Validate password
 * - Check account verification
 * - Handle login success and failure
 * - Protect against CSRF attacks
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
     * @param UserRepository $userRepository Repository used to fetch users
     */
    public function __construct(
        RouterInterface $router,
        UserRepository $userRepository
    ) {
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    /**
     * Handles authentication request (login form submission)
     *
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        // Retrieve credentials from request
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        // Store last username for better UX
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
                        'Email non trouvé.'
                    );
                }

                // Check if account is actived
                if (!$user->isActive()) {
                    throw new CustomUserMessageAuthenticationException(
                        'Veuillez vérifier votre email avant de vous connecter.'
                    );
                }

                return $user;
            }),

            // Password verification handled automatically
            new PasswordCredentials($password),

            [
                // CSRF protection
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    /**
     * Called when authentication succeeds
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

        // Redirect to previously requested page if exists
        if ($targetPath = $this->getTargetPath(
            $request->getSession(),
            $firewallName
        )) {
            return new Response('', Response::HTTP_FOUND, [
                'Location' => $targetPath
            ]);
        }

        // Default redirection (home page)
        return new RedirectResponse(
        $this->router->generate('app_home')
        );
    }

    /**
     * Called when authentication fails
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): Response {

        // Store error in session
        $request->getSession()->set(
            SecurityRequestAttributes::AUTHENTICATION_ERROR,
            $exception
        );

        return new RedirectResponse(
        $this->getLoginUrl($request)
        );
    }

    /**
     * Returns login URL
     *
     * @param Request $request
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}