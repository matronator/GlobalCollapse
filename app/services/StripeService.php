<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Nette\Database\Table\ActiveRow;
use Stripe\Checkout\Session;
use Stripe\Customer;
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

    public function createCheckoutSession(string $priceId, ActiveRow $user, string $mode = 'subscription'): Session
    {
        if ($user->stripe_customer_id === null) {
            $customer = $this->stripeClient->customers->create([
                'email' => $user->email,
                'description' => $user->username,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);
            $user->update([
                'stripe_customer_id' => $customer->id,
            ]);
        }

        try {
            $customer = Customer::retrieve($user->stripe_customer_id);
            if (!$customer) {
                $user->update([
                    'stripe_customer_id' => null,
                ]);
                return $this->createCheckoutSession($priceId, $user, $mode);
            }
        } catch (Exception $e) {
            $user->update([
                'stripe_customer_id' => null,
            ]);
            return $this->createCheckoutSession($priceId, $user, $mode);
        }

        return Session::create([
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'customer' => $user->stripe_customer_id,
            'allow_promotion_codes' => true,
            "automatic_tax" => [
                "enabled" => true,
            ],
            'consent_collection' => [
                'terms_of_service' => 'required',
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ],
            'customer_update' => [
                'address' => 'auto',
                'shipping' => 'auto',
            ],
            'mode' => $mode,
            'metadata' => [
                'user_id' => $user->id,
            ],
            'success_url' => $this->appUrl . '/premium/success?sessionId={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->appUrl . '/premium/cancel',
        ]);
    }
}
