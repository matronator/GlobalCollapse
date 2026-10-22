<?php

declare(strict_types=1);

namespace App\Types\Gambling;

class CoinFlipGameConfig
{
    public bool $enabled;
    public ?int $min_bet;
    public ?int $max_bet;
    public ?float $win_chance;
    public float $payout_multiplier;
    /** @var CoinFlipBetConfig[] */
    public ?array $bets;

    public function __construct(array $config)
    {
        $this->enabled = $config['enabled'] ?? false;
        $this->min_bet = $config['min_bet'] ?? 1;
        $this->max_bet = $config['max_bet'] ?? 5;
        $this->win_chance = $config['win_chance'] ?? 0.5;
        $this->payout_multiplier = $config['payout_multiplier'] ?? 2.0;
        $this->bets = $config['bets'] ?? null;
    }
}
