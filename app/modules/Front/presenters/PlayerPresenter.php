<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class PlayerPresenter extends BasePresenter
{

	private $userRepository;

	public function __construct(
		UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDetail(?string $user = null) {
		if (!$user) {
			$this->redirect('Default:default');
		}
		$otherPlayer = $this->userRepository->getUserByName($user);
		if ($otherPlayer) {
			$reward = $this->getRewards($otherPlayer);
			$this->template->otherPlayer = $otherPlayer;
			$this->template->cashMoney = $reward['win_money'];
			$this->template->cashMoneyLose = $reward['lose_money'];
		} else {
			$this->error();
		}
	}

	public function renderAssault(int $id) {
		if (!$id) {
			$this->redirect('Default:default');
		}
		$assault = $this->assaultPlayer($id);
		$result = json_decode($assault);
		$this->template->rounds = $result->rounds;
		$this->template->roundCount = count($result->rounds);
		$this->template->result = $result->result;
		$this->template->attacker = $result->attacker;
		$this->template->victim = $result->victim;
		$otherPlayer = $this->userRepository->getUser($id);
		if ($otherPlayer) {
			$reward = $this->getRewards($otherPlayer);
			$this->template->otherPlayer = $otherPlayer;
			$this->template->cashMoney = $reward['win_money'];
			$this->template->cashMoneyLose = $reward['lose_money'];
		} else {
			$this->error();
		}
	}

	public function getRewards($victim) {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$victimMoney = $victim->money;
		$attackerLevel = $player->player_stats->level;
		$attackerPower = $player->player_stats->power;
		$victimLevel = $victim->player_stats->level;
		$victimPower = $victim->player_stats->power;
		$winReward = [];

		$ratio = max(10, (((3 * $victimPower) - (2 * $attackerPower)) / $attackerPower) * ((3 * $victimLevel) / $attackerLevel));
		$percentage = min($ratio, 100);
		$winReward['win_money'] = round(($victimMoney / 100) * $percentage);
		$winReward['win_xp'] = round(2 * $ratio);

		$ratio = max(10, (((3 * $attackerPower) - (2 * $victimPower)) / $victimPower) * ((3 * $attackerLevel) / $victimLevel));
		$percentage = min($ratio, 50);
		$winReward['lose_money'] = round(($player->money / 100) * $percentage);

		return $winReward;
	}

	public function assaultPlayer(int $id) {
		$player = $this->userRepository->findUsers('power')->get($this->user->getIdentity()->id);
		$victim = $this->userRepository->getUser($id);
		$astats = $player->player_stats;
		$vstats = $victim->player_stats;
		$playerHealth = $astats->stamina * 2;
		$victimHealth = $vstats->stamina * 2;
		$rounds = [];
		$i = 0;
		$result = '';

		$rounds[$i] = [
			"id" => $i,
			"attacker" => [
				"hp" => $playerHealth,
				"dmg" => null
			],
			"victim" => [
				"hp" => $victimHealth,
				"dmg" => null
			],
			"status" => "start"
		];
		$i++;

		while ($playerHealth > 0 && $victimHealth > 0) {
			$round = $this->assaultRound($astats, $vstats, $victimHealth);
			$continue = $round['status'] == 'kill' ? false : true;
			$aDamage = $round['dmg'];
			$victimHealth = $round['hp'];
			if (!$continue) {
				$rounds[$i] = [
					"id" => $i,
					"attacker" => [
						"hp" => $playerHealth,
						"dmg" => $aDamage
					],
					"victim" => [
						"hp" => $victimHealth,
						"dmg" => null
					],
					"status" => "end"
				];
				$result = 'win';
				break;
			} else {
				$round = $this->assaultRound($vstats, $astats, $playerHealth);
				$continue = $round['status'] == 'kill' ? 'end' : 'continue';
				$vDamage = $round['dmg'];
				$playerHealth = $round['hp'];
				$rounds[$i] = [
					"id" => $i,
					"attacker" => [
						"hp" => $playerHealth,
						"dmg" => $aDamage
					],
					"victim" => [
						"hp" => $victimHealth,
						"dmg" => $vDamage
					],
					"status" => $continue
				];
				if ($continue == 'end') {
					$result = 'defeat';
					break;
				}
				$i++;
			}
		}
		$json = [
			"attacker" => $player->username,
			"victim" => $victim->username,
			"result" => $result,
			"rounds" => $rounds
		];

		return json_encode($json);
	}

	private function assaultRound($astats, $vstats, $vHp) {
		$results = [];
		$aDmg = $this->calculateDamage($astats, $vstats);
		$realDmgA = $aDmg == 'dodged' ? 0 : $aDmg;
		$vHp -= $realDmgA;
		$results['status'] = 'continue';
		if ($vHp <= 0) {
			$results['status'] = 'kill';
		}
		$results['dmg'] = $aDmg;
		$results['hp'] = $vHp;

		return $results;
	}

	private function calculateDamage($attackerStats, $victimStats) {
		$attackerDodge = 0;
		$dodgeChance = min(($victimStats->speed - $attackerStats->speed) + (($victimStats->level - $attackerStats->level) / 2), 90);
		if ($dodgeChance > 0) {
			$attackerDodge = $dodgeChance;
		}
		$dodged = rand(0, 100) <= $attackerDodge ? true : false;
		if ($dodged) {
			return 'dodged';
		} else {
			return round(max($attackerStats->strength - ($victimStats->strength / rand(3, 5)), 0));
		}
	}

	/**
	 *
	 * --------------- Reward formula -------------------
	 * --------------------------------------------------
	 *
	 * AL = Attacker level           VL = Victim level
	 * AP = Attacker power           VP = Victim power
	 *
	 *          /    3*VP - 2*AP     3*VL  \
	 * v = MAX | 1; ------------- * ------- |
	 *          \        AP           AL   /
	 *
	 * p = MIN (p; 50)
	 *
	 *  VM = Victim money
	 *
	 *               R = p% of VM
	 *
	 *           R = ( VM / 100 ) * p
	 *
	 *
	 */

	/**
	 *
	 * ------- Dodge formula --------
	 *
	 * AL = Attacker level           VL = Victim level
	 * AS = Attacker speed           VS = Victim speed
	 * D = Dodge chance
	 *
	 * D = MIN ( (AS - VS) + ((AL - VL) / 2), 90 )
	 *
	 */
}
