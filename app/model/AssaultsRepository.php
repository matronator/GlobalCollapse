<?php

namespace App\Model;

use Nette;

class AssaultsRepository
{
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAllAssaultStats()
	{
		return $this->database->table('assault_stats');
	}

	public function findPlayerAssaultStats(int $userId)
	{
		return $this->database->table('assault_stats')->where('id', $userId);
	}

	public function addAssaultVictory(int $attackerId, int $victimId)
	{
		$attackerStats = $this->findPlayerAssaultStats($attackerId);
		if ($attackerStats->count() > 0) {
			$attackerStats->update([
				"attacks_won+=" => 1,
				"total_attacks+=" => 1,
				"total+=" => 1
			]);
		} else {
			$attackerStats->insert([
				"user_id" => $attackerId,
				"attacks_won" => 1,
				"total_attacks" => 1,
				"total" => 1
			]);
		}
		$victimStats = $this->findPlayerAssaultStats($victimId);
		if ($victimStats->count() > 0) {
			$victimStats->update([
				"defenses_lost+=" => 1,
				"total_defenses+=" => 1,
				"total+=" => 1
			]);
		} else {
			$victimStats->insert([
				"user_id" => $victimId,
				"defenses_lost" => 1,
				"total_defenses" => 1,
				"total" => 1
			]);
		}
		return $attackerStats;
	}

	public function addAssaultDefeat(int $attackerId, int $victimId)
	{
		$attackerStats = $this->findPlayerAssaultStats($attackerId);
		if ($attackerStats->count() > 0) {
			$attackerStats->update([
				"attacks_lost+=" => 1,
				"total_attacks+=" => 1,
				"total+=" => 1
			]);
		} else {
			$attackerStats->insert([
				"user_id" => $attackerId,
				"attacks_lost" => 1,
				"total_attacks" => 1,
				"total" => 1
			]);
		}
		$victimStats = $this->findPlayerAssaultStats($victimId);
		if ($victimStats->count() > 0) {
			$victimStats->update([
				"defenses_won+=" => 1,
				"total_defenses+=" => 1,
				"total+=" => 1
			]);
		} else {
			$victimStats->insert([
				"user_id" => $victimId,
				"defenses_won" => 1,
				"total_defenses" => 1,
				"total" => 1
			]);
		}
		return $attackerStats;
	}
}
