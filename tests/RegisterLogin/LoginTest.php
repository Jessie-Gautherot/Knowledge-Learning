<?php

namespace App\Tests\RegisterLogin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    /**
    * Check that an activate user can log in.
    *
    * This test checks that:
    * - the login page works
    * - the login form can be submitted
    * - the user is redirected after login
    */
    public function testUserCanLogin(): void
    {
        // Create a test client.
        $client = static::createClient();

        // Open the login page.
        $crawler = $client->request('GET', '/login');

        // Get the CSRF token from the form.
        $csrfToken = $crawler
            ->filter('input[name="_csrf_token"]')
            ->attr('value');

        // Submit the login form.
        $client->request('POST', '/login', [
            'email' => 'client@test.com',
            'password' => 'Password1',
            '_csrf_token' => $csrfToken,
        ]);

        // Check the redirect after login.
        $this->assertResponseRedirects('/');
    }
}