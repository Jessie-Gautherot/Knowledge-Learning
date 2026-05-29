<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Service used to send emails.
 */
class EmailService
{
    private MailerInterface $mailer;
    private string $appUrl;

    public function __construct(MailerInterface $mailer, string $appUrl)
    {
        $this->mailer = $mailer;
        $this->appUrl = $appUrl;
        
    }


    /**
     * Send an account activation email to a newly registered user.
     *
     * @param string $to User email address
     * @param string $token Activation token used to generate the activation link
     */
    public function sendActivationEmail(string $to, string $token): void
    {
      // Build the activation link
      $activationLink = $this->appUrl . '/activate/' . $token;

        $email = (new Email())
            ->from('jessie.gautherot@gmail.com')
            ->to($to)
            ->subject('Activation de votre compte')
            ->html("
            <h1>Activation de votre compte</h1>

            <p>
                Cliquez sur le lien ci-dessous pour activer votre compte :
            </p>

            <a href='$activationLink'>
                Activer mon compte
            </a>
            ");

        // Send email via Symfony Mailer
        $this->mailer->send($email);
    }
}