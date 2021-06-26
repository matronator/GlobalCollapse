<?php

namespace App\Model;

use Nette;

class AssaultsRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function findAllAssaultStats()
	{
		return $this->database->table('assault_stats');
	}

	public function findPlayerAssaultStats(int $userId)
	{
		return $this->database->table('assault_stats')->where('user_id', $userId);
	}

	private function findAllAssaults()
	{
		return $this->database->table('assaults');
	}

	/**
	 * Save the assault record to database
	 *
	 * @param integer $attackerId
	 * @param integer $defenderId
	 * @param DateTime $date
	 * @param string $result
	 * @param string $aName attacker username
	 * @param string $vName victim username
	 * @param string $data assault replay in JSON
	 * @return int returns ID of the assault record
	 */
	public function recordAssault(int $attackerId, int $defenderId, $date, string $result, string $aName, string $vName, $data): int
	{
		$assault = $this->findAllAssaults()->insert([
			'attacker' => $attackerId,
			'defender' => $defenderId,
			'result' => $result,
			'attacker_name' => $aName,
			'victim_name' => $vName,
			'date' => $date
		]);
		$this->saveAssaultData($assault->id, $data);
		return $assault->id;
	}

	public function getLatestAssaults(int $userId)
	{
		return $this->findAllAssaults()->whereOr(['attacker' => $userId, 'defender' => $userId])->order('date DESC')->limit(10);
	}

	/**
	 * Save the assault replay in JSON
	 *
	 * @param integer $assaultId
	 * @param string $data
	 * @return void
	 */
	private function saveAssaultData(int $assaultId, $data)
	{
		$this->database->table('assault_replays')->insert([
			'assault_id' => $assaultId,
			'data' => $data
		]);
	}

	public function addAssaultVictory(int $attackerId, int $victimId, int $assaultId)
	{
		$attackerStats = $this->findPlayerAssaultStats($attackerId)->fetch();
		if (isset($attackerStats->total)) {
			$this->findPlayerAssaultStats($attackerId)->update([
				"attacks_won+=" => 1,
				"total_attacks+=" => 1,
				"total+=" => 1,
				"last_attack" => $assaultId
			]);
		} else {
			$this->findPlayerAssaultStats($attackerId)->insert([
				"user_id" => $attackerId,
				"attacks_won" => 1,
				"total_attacks" => 1,
				"total" => 1,
				"last_attack" => $assaultId
			]);
		}
		$victimStats = $this->findPlayerAssaultStats($victimId)->fetch();
		if (isset($victimStats->total)) {
			$this->findPlayerAssaultStats($victimId)->update([
				"defenses_lost+=" => 1,
				"total_defenses+=" => 1,
				"total+=" => 1,
				"last_defense" => $assaultId
			]);
		} else {
			$this->findPlayerAssaultStats($victimId)->insert([
				"user_id" => $victimId,
				"defenses_lost" => 1,
				"total_defenses" => 1,
				"total" => 1,
				"last_defense" => $assaultId
			]);
		}
		return $attackerStats;
	}

	public function addAssaultDefeat(int $attackerId, int $victimId, int $assaultId)
	{
		$attackerStats = $this->findPlayerAssaultStats($attackerId)->fetch();
		if (isset($attackerStats->total)) {
			$this->findPlayerAssaultStats($attackerId)->update([
				"attacks_lost+=" => 1,
				"total_attacks+=" => 1,
				"total+=" => 1,
				"last_attack" => $assaultId
			]);
		} else {
			$this->findPlayerAssaultStats($attackerId)->insert([
				"user_id" => $attackerId,
				"attacks_lost" => 1,
				"total_attacks" => 1,
				"total" => 1,
				"last_attack" => $assaultId
			]);
		}
		$victimStats = $this->findPlayerAssaultStats($victimId)->fetch();
		if (isset($victimStats->total)) {
			$this->findPlayerAssaultStats($victimId)->update([
				"defenses_won+=" => 1,
				"total_defenses+=" => 1,
				"total+=" => 1,
				"last_defense" => $assaultId
			]);
		} else {
			$this->findPlayerAssaultStats($victimId)->insert([
				"user_id" => $victimId,
				"defenses_won" => 1,
				"total_defenses" => 1,
				"total" => 1,
				"last_defense" => $assaultId
			]);
		}
		return $attackerStats;
	}
}
