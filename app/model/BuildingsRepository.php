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
		$newLand = $this->findPlayerLand($userId)->insert([
			'user_id' => $userId
		]);
		$buildings = $this->findAllBuildings()->where('unlocked', 1);
		foreach($buildings as $building) {
			$this->findAllPlayerBuildings($userId)->insert([
				'buildings_id' => $building->id,
				'user_id' => $userId,
				'player_land_id' => $newLand->id,
				'level' => 0
			]);
		}
	}

	public function findPlayerIncome(?int $userId = null)
	{
		return $this->database->table('player_income')->where('user_id', $userId);
	}

	public function buyBuilding(int $userId = 0, int $bId = 0)
	{
		$checkLocked = $this->findAllUnlocked($userId)->where('buildings_id', $bId);
		if ($checkLocked) {
			$checkFreeLand = $this->findPlayerLand($userId)->where('free_slots > ?', 0)->fetch();
			if ($checkFreeLand) {
				$origBuilding = $this->findAllBuildings()->where('id', $bId)->fetch();
				$income = $origBuilding->base_income;
				$incomeType = '';
				switch ($origBuilding->name) {
					case 'weedhouse':
						$incomeType = 'weed';
						break;
					case 'meth_lab':
						$incomeType = 'meth';
						break;
					case 'ecstasy_lab':
						$incomeType = 'ecstasy';
						break;
					case 'poppy_field':
						$incomeType = 'heroin';
						break;
					case 'coca_plantage':
						$incomeType = 'coke';
						break;
				}
				if ($incomeType != '') {
					$playerIncome = $this->findPlayerIncome($userId)->fetch();
					if (!$playerIncome) {
						$this->findPlayerIncome($userId)->insert([
							'user_id' => $userId,
							$incomeType => $income
						]);
					} else {
						if (!$playerIncome->$incomeType) {
							$this->findPlayerIncome($userId)->update([
								$incomeType => $income
							]);
						} else {
							$this->findPlayerIncome($userId)->update([
								$incomeType . '+=' => $income
							]);
						}
					}
				}
				$this->findPlayerLand($userId)->update([
					'free_slots-=' => 1
				]);
				$this->findAllPlayerBuildings($userId)->where('buildings_id', $bId)->update([
					'level' => 1,
					'income' => $this->findAllBuildings()->where('id', $bId)->fetch()->base_income
				]);
				return $this->findAllPlayerBuildings($userId)->insert([
					'buildings_id' => $bId,
					'user_id' => $userId,
					'player_land_id' => $checkFreeLand->id,
					'level' => 0
				]);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getLandPrice()
	{
		return $this->newLandPrice;
	}

	// Building income = baseIncome + round(baseIncome * ((level-1)/2)^1.05)
	public function getBuildingIncome(int $baseIncome = 0, int $level = 1)
	{
		return $baseIncome + round($baseIncome * pow(($level - 1) / 2, 1.05));
	}
}
