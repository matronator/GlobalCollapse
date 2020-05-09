<?php

namespace App\Model;

use Nette;
use Nette\Utils\ArrayHash;

class BuildingsRepository
{
	/** @var Nette\Database\Context */
	private $database;

	private $newLandPrice = 1000;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAllBuildings()
	{
		return $this->database->table('buildings');
	}

	public function findBuildingByKey(string $key = 'id', $value = null)
	{
		if ($key == 'id') {
			$value = intval($value);
		}
		return $this->database->table('buildings')->where($key, $value);
  }

	public function findAllPlayerBuildings(?int $userId = null)
	{
		return $this->database->table('player_buildings')->where('user_id', $userId);
	}

	public function findAllUnlocked(?int $userId = null)
	{
		return $this->findAllPlayerBuildings($userId)->where('level', 0);
	}

	public function findPlayerBuildings(?int $userId = null)
	{
		return $this->findAllPlayerBuildings($userId)->where('level > ?', 0);
	}

	public function findPlayerLand(?int $userId = null)
	{
		return $this->database->table('player_lands')->where('user_id', $userId);
	}

	public function buyLand(?int $userId = null)
	{
		$this->findPlayerLand($userId)->insert([
			'user_id' => $userId
		]);
		$buildings = $this->findAllBuildings()->where('unlocked', 1);
		foreach($buildings as $building) {
			$this->findAllPlayerBuildings($userId)->insert([
				'buildings_id' => $building->id,
				'user_id' => $userId,
				'level' => 0
			]);
		}
	}

	public function getLandPrice()
	{
		return $this->newLandPrice;
	}
}
