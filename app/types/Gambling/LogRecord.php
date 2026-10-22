<?php

declare(strict_types=1);

namespace App\Types\Gambling;

use App\Model\CoinFlipGameResult;
use App\Model\Result;

class LogRecord
{
    public int $user_id;
    public string $game;
    public int $bet_amount;
    public int $win_amount;
    public Result $result;
    public ?CoinFlipGameResult $game_result;
    public \DateTime $created_at;

    public function __construct(
        int $user_id,
        string $game,
        int $bet_amount,
        int $win_amount,
        Result $result,
        ?CoinFlipGameResult $game_result,
        \DateTime $created_at
    ) {
        $this->user_id = $user_id;
        $this->game = $game;
        $this->bet_amount = $bet_amount;
        $this->win_amount = $win_amount;
        $this->result = $result;
        $this->game_result = $game_result;
        $this->created_at = $created_at;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'game' => $this->game,
            'bet_amount' => $this->bet_amount,
            'win_amount' => $this->win_amount,
            'result' => $this->result->value,
            'game_result' => $this->game_result?->value ?? null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
