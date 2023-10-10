<?php

declare(strict_types=1);

namespace App\Services;

class PaymentService
{
    public function __construct(public string $psp = 'stripe')
    {
    }

    public function getPsp(): string
    {
        return $this->psp;
    }

    public function isStripe(): bool
    {
        return $this->psp === 'stripe';
    }
}
