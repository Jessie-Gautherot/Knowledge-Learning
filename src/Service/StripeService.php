<?php

namespace App\Service;

use Stripe\StripeClient;

/**
 * Class StripeService
 *
 * Handles Stripe Checkout payments in sandbox.
 */
class StripeService
{
    /**
     * Constructor
     */
    public function __construct(
        private StripeClient $stripeClient
    ) {
    }

    /**
     * Create a Stripe Checkout session.
     *
     * @param string $name Product name
     * @param int $amount Price in cents
     * @param string $successUrl URL after successful payment
     * @param string $cancelUrl URL after canceled payment
     * @return string Stripe checkout URL
     */
    public function createCheckoutSession(
        string $name,
        int $amount,
        string $successUrl,
        string $cancelUrl
    ): string {
        $session = $this->stripeClient->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $name,
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return $session->url;
    }
}