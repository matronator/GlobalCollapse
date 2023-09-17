<?php

declare(strict_types=1);

namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripeService
{
    public string $publicKey;
    public string $webhookSecret;
    private string $secretKey;

    public string $appUrl;

    public StripeClient $stripeClient;

    public function __construct(array $config)
    {
        $this->publicKey = $config['pubKey'];
        $this->secretKey = $config['secret'];
        $this->appUrl = $config['appUrl'];
        $this->webhookSecret = $config['webhookSecret'];

        Stripe::setApiKey($this->secretKey);
        $this->stripeClient = new StripeClient($this->secretKey);
    }

    public function createCheckoutSession(string $lookupKey): object
    {
        $prices = Price::all([
            'lookup_keys' => [$lookupKey],
            'expand' => 'data.product',
        ]);

        return Session::create([
            'line_items' => [[
                'price' => $prices->data[0]->id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $this->appUrl . '/success.html?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->appUrl . '/cancel.html',
        ]);
    }
}
