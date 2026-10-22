<?php

declare(strict_types=1);

namespace App\Model;

use App\Types\Gambling\CoinFlipGameConfig;
use App\Types\Gambling\LogRecord;
use DateTime;
use Nette;
use \Random\Randomizer;

enum Result: string
{
	case WIN = 'WIN';
	case LOSE = 'LOSE';
	case DRAW = 'DRAW';
}

enum CoinFlipGameResult: string
{
	case HEADS = 'HEADS';
	case TAILS = 'TAILS';
}

class GamblingRepository
{
	public const GAME_COIN_FLIP = 'COIN_FLIP';

	public const GAMES = [
		self::GAME_COIN_FLIP,
	];

	public const RESULT_WIN = 'WIN';
	public const RESULT_LOSE = 'LOSE';
	public const RESULT_DRAW = 'DRAW';
	public const RESULTS = [
		self::RESULT_WIN,
		self::RESULT_LOSE,
		self::RESULT_DRAW,
	];

	public const COIN_FLIP_RESULT_HEADS = 'HEADS';
	public const COIN_FLIP_RESULT_TAILS = 'TAILS';
	public const COIN_FLIP_RESULTS = [
		self::COIN_FLIP_RESULT_HEADS,
		self::COIN_FLIP_RESULT_TAILS,
	];

	public CoinFlipGameConfig $coinFlipGameConfig;

	private Randomizer $randomizer;

	public function __construct(private array $gamblingConfig, private Nette\Database\Explorer $database)
	{
		$this->coinFlipGameConfig = new CoinFlipGameConfig($this->gamblingConfig['games'][strtolower(self::GAME_COIN_FLIP)]);
		$this->randomizer = new Randomizer(new \Random\Engine\Secure());
	}

	public function findAllLogs()
	{
		return $this->database->table('gambling_logs');
	}

	public function playCoinFlip(int $userId, int $betAmount, CoinFlipGameResult $betChoice): LogRecord
	{
		$chance = 0.5;
		if ($this->coinFlipGameConfig->bets !== null) {
			if (!$this->coinFlipGameConfig->bets[$betAmount]) {
				throw new \InvalidArgumentException('Bet amount not allowed');
			}
			$chance = $this->coinFlipGameConfig->bets[$betAmount]->win_chance;
		} else {
			if ($betAmount < $this->coinFlipGameConfig->min_bet || $betAmount > $this->coinFlipGameConfig->max_bet) {
				throw new \InvalidArgumentException('Bet amount not allowed');
			}
			$chance = $this->coinFlipGameConfig->win_chance;
		}

		$result = $this->randomizer->getInt(1, 100) <= ($chance * 100) ? Result::WIN : Result::LOSE;
		$winAmount = 0;
		$coinSide = null;
		if ($result === Result::WIN) {
			$coinSide = $betChoice;
			$winAmount = (int) round($betAmount * $this->coinFlipGameConfig->payout_multiplier);
		} else {
			if ($betChoice === CoinFlipGameResult::HEADS) {
				$coinSide = CoinFlipGameResult::TAILS;
			} else {
				$coinSide = CoinFlipGameResult::HEADS;
			}
			$winAmount = 0;
		}

		return $this->recordLog($userId, self::GAME_COIN_FLIP, $betAmount, $winAmount, $result, $coinSide);
	}

	public function recordLog(int $userId, string $game, int $betAmount, int $winAmount, Result $result, ?CoinFlipGameResult $gameResult = null): LogRecord
	{
		$this->findAllLogs()->insert([
			'user_id' => $userId,
			'game' => $game,
			'bet_amount' => $betAmount,
			'win_amount' => $winAmount,
			'result' => $result,
			'game_result' => $gameResult,
			'created_at' => new DateTime(),
		]);

		return new LogRecord(
			user_id: $userId,
			game: self::GAME_COIN_FLIP,
			bet_amount: $betAmount,
			win_amount: $winAmount,
			result: $result,
			game_result: $gameResult,
			created_at: new DateTime(),
		);
	}
}
