<?php

declare(strict_types=1);

namespace App\Types\Gambling;

class CoinFlipBetConfig
{
    public float $win_chance;

    public function __construct(array $config)
    {
        $this->win_chance = $config['win_chance'] ?? 0.5;
    }
}
