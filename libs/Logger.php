<?php

declare(strict_types=1);

use Tracy\ILogger;

interface Logger extends ILogger
{
    public const STRIPE = 'stripe';
    public const CSP = 'csp';
}
