<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\AssaultsRepository;
use DateTime;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class AssaultsPresenter extends GamePresenter
{

	private $userRepository;

	/** @var Model\AssaultsRepository */
  private $assaultsRepository;

	public function __construct(
		UserRepository $userRepository,
		AssaultsRepository $assaultsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->assaultsRepository = $assaultsRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDefault() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$aStatsP = $this->assaultsRepository->findPlayerAssaultStats($player->id)->fetch();
		$latestAssaults = $this->assaultsRepository->getLatestAssaults($player->id);
		$this->template->aStatsP = $aStatsP;
		$this->template->latestAssaults = $latestAssaults;
	}

	public function renderDetail(?string $user = null) {
		if (!$user || $user == $this->userRepository->getUser($this->user->getIdentity()->id)->username) {
			$this->redirect('Default:default');
		} else {
			$otherPlayer = $this->userRepository->getUserByName($user);
			if ($otherPlayer) {
				$reward = $this->getRewards($otherPlayer);
				$sessionSection = $this->session->getSection('assault');
				$sessionSection['victim'] = $otherPlayer->id;
				$this->template->otherPlayer = $otherPlayer;
				$this->template->cashMoney = $reward['win_money'];
				$this->template->xpReward = $reward['win_xp'];
				$this->template->cashMoneyLose = $reward['lose_money'];
				$aStatsP = $this->assaultsRepository->findPlayerAssaultStats($this->user->getIdentity()->id);
				$this->template->aStatsP = $aStatsP->fetch();
			} else {
				$this->error();
			}
		}
	}

	public function renderAssault(?string $match = null) {
		// check hash
		$sessionSection = $this->session->getSection('assault');
		if (isset($sessionSection['hash']) && $sessionSection['hash'] == $match) {
			$playerId = $this->user->getIdentity()->id;
			$id = $sessionSection['victim'];
			$otherPlayer = $this->userRepository->getUser($id);
			// check if victim exists and isn't the current player
			if ($otherPlayer && $playerId != $otherPlayer->id) {
				$this->template->otherPlayer = $otherPlayer;
				$jsonResult = $sessionSection['results'];
				$result = json_decode($jsonResult);
				unset($sessionSection['results']);
				unset($sessionSection['victim']);
				unset($sessionSection['hash']);
				unset($sessionSection);
				$assaultResult = $result->result;

				// Save assault record
				$assaultId = $this->assaultsRepository->recordAssault($playerId, $otherPlayer->id, new DateTime(), $assaultResult, $result->attacker, $otherPlayer->username, $jsonResult);

				$this->template->rounds = $result->rounds;
				$this->template->roundCount = count($result->rounds);
				$this->template->result = $assaultResult;
				$this->template->attacker = $result->attacker;
				$this->template->victim = $result->victim;
				// get the rewards
				$reward = $this->getRewards($otherPlayer);
				$winReward = $reward['win_money'];
				$xpReward = $reward['win_xp'];
				$defeatPenalty = $reward['lose_money'];
				if ($assaultResult == 'win') {
					$this->userRepository->addMoney($playerId, $winReward);
					if ($xpReward > 0) {
						$this->assaultsRepository->addAssaultVictory($playerId, $otherPlayer->id, $assaultId);
						$this->userRepository->addXp($playerId, $xpReward);
					}
					$this->userRepository->addMoney($otherPlayer->id, round($winReward / 10 * (-1), 0));
				} else {
					$this->assaultsRepository->addAssaultDefeat($playerId, $otherPlayer->id, $assaultId);
					$this->userRepository->addMoney($playerId, $defeatPenalty * (-1));
				}
				$this->template->cashMoney = $winReward;
				$this->template->cashMoneyLose = $defeatPenalty;
				$this->template->xpReward = $xpReward;
			} else {
				$this->redirect('Default:default');
			}
		} else {
			$this->redirect('Default:default');
		}
	}

	public function renderReplay() {

	}

	public function getPlayerInfo(string $username)
	{
		return $this->userRepository->getUserByName($username);
	}

	public function actionAttack() {
		$sessionSection = $this->session->getSection('assault');
		$id = $sessionSection['victim'];
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		if (!$id || (isset($id) && $id == $player->id)) {
			$this->redirect('Default:default');
		} else {
			$otherPlayer = $this->userRepository->getUser($id);
			if (!$otherPlayer) {
				$this->redirect('Default:default');
			} else {
				if ($player->id == $otherPlayer->id) {
					$this->redirect('Default:default');
				} else {
					if ($player->player_stats->energy < 10) {
						$this->flashMessage('Not enough energy!', 'warning');
						$this->redirect('Default:rest');
					} else {
						// assault
						$assault = $this->assaultPlayer($otherPlayer->id);
						$result = json_decode($assault);
						$sessionSection['results'] = $assault;
						$sessionSection['victim'] = $otherPlayer->id;
						// hash
						$timestamp = (string)time();
						$bytes = random_bytes(5);
						$hash = $timestamp . bin2hex($bytes);
						$sessionSection['hash'] = $hash;
						// redirect to animation
						$this->userRepository->addEnergy($player->id, -10);
						$this->redirect('Assaults:assault', $hash);
					}
				}
			}
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

		$ratio = max(0, (((3 * $victimPower) - ($attackerPower)) / sqrt($attackerPower)) * ((3 * $victimLevel) / $attackerLevel));
		$percentage = min($ratio, 10);
		$winReward['win_money'] = (int)round(($victimMoney / 100) * $percentage);
		$winReward['win_xp'] = round(min($ratio, 10 + min($attackerLevel / 7, 25)));

		$ratio = max(0, (((3 * $attackerPower) - (2 * $victimPower)) / sqrt($victimPower)) * ((3 * $attackerLevel) / $victimLevel));
		$percentage = min($ratio, 25);
		$winReward['lose_money'] = (int)round(min(max(($player->money / 100) * $percentage, 50), $player->money));

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
	 * The victim will only lose 10% of that, but the attacker gets the full amount
	 * If the victim's level is under 10, they don't lose any money
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
