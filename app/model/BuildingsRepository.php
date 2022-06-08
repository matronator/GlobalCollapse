<?php

namespace App\Model;

use DateTime;
use Nette;
use Nette\Utils\ArrayHash;

class BuildingsRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	private $newLandPrice = 1000;
	public $baseUpgradeCost = 50000;
	public $baseUpgradeTime = 3600; // 1 hour

	public function __construct(Nette\Database\Explorer $database)
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

	public function getBuilding(int $id)
	{
		return $this->database->table('player_buildings')->where('id', $id);
  }

	public function findAllPlayerBuildings(?int $userId = null)
	{
		return $this->database->table('player_buildings')->where('user_id', $userId);
	}

	public function findAllUnlocked(?int $userId = null)
	{
		return $this->findAllPlayerBuildings($userId)->where('level <= ?', 0);
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

	public function startLandUpgrade(int $userId)
	{
		$land = $this->findPlayerLand($userId);
		if ($land->count() > 0) {
			$landData = $this->findPlayerLand($userId)->fetch();
			$this->pauseProduction($userId);
			$now = new DateTime();
			$upgradeEndTS = $now->getTimestamp();
			$upgradeEndTS += $this->getLandUpgradeTime($landData->level);
			$now->setTimestamp($upgradeEndTS);
			$upgradeEnd = $now->format('Y-m-d H:i:s');
			$this->findAllPlayerBuildings($userId)->update([
				'is_upgrading' => 1,
				'upgrade_end' => $upgradeEnd
			]);
			$land->update([
				'is_upgrading' => 1,
				'upgrade_end' => $upgradeEnd
			]);
			return $upgradeEnd;
		} else {
			return false;
		}
	}

	public function upgradeLand(int $userId)
	{
		$landData = $this->findPlayerLand($userId)->fetch();
		if ($landData && isset($landData->is_upgrading) && $landData->is_upgrading == 1) {
			$slotsAdd = $this->getLandSlotGain($landData->level);
			$land = $this->findPlayerLand($userId);
			$this->pauseProduction($userId, true);
			$this->findAllPlayerBuildings($userId)->update([
				'is_upgrading' => 0
			]);
			$land->update([
				'is_upgrading' => 0,
				'level+=' => 1,
				'slots+=' => $slotsAdd,
				'free_slots+=' => $slotsAdd
			]);
		}
	}

	public function pauseProduction(int $userId, bool $resume = false)
	{
		$income = $this->findPlayerIncome($userId);
		$pause = $resume ? 0 : 1;
		if ($income->count() > 0) {
			$income->update([
				'paused' => $pause
			]);
		} else {
			$income->insert([
				'user_id' => $userId,
				'paused' => $pause
			]);
		}
	}

	public function getLandSlotGain(int $level)
	{
		$slotsAdd = 1;
		switch (true) {
			case in_array($level, range(1, 10)):
				$slotsAdd = 1;
			break;
			case in_array($level, range(11, 25)):
				$slotsAdd = 2;
			break;
			case ($level > 25):
				$slotsAdd = 3;
			break;
		}
		return $slotsAdd;
	}

	public function findPlayerIncome(?int $userId = null)
	{
		return $this->database->table('player_income')->where('user_id', $userId);
	}

	public function buyBuilding(int $userId = 0, int $bId = 0, ?int $buildingsId)
	{
		$checkLocked = $this->getBuilding($buildingsId)->fetch();
		if (isset($checkLocked->level) && $checkLocked->level === 0) {
			$checkFreeLand = $this->findPlayerLand($userId)->where('free_slots > ?', 0)->fetch();
			if (isset($checkFreeLand)) {
				$origBuilding = $this->findAllBuildings()->where('id', $bId)->fetch();
				$income = $origBuilding->base_income;
				$incomeType = $this->getIncomeType($origBuilding->name);
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
				$originalBuilding = $this->findAllBuildings()->where('id', $bId)->fetch();
				$this->findPlayerLand($userId)->update([
					'free_slots-=' => 1
				]);
				$this->getBuilding($buildingsId)->update([
					'level' => 1,
					'income' => $originalBuilding->base_income,
					'capacity' => $originalBuilding->base_capacity
				]);
				$this->findPlayerBuildings($userId)->insert([
					'buildings_id' => $bId,
					'user_id' => $userId,
					'player_land_id' => $checkFreeLand->id,
					'level' => 0,
					'capacity' => $originalBuilding->base_capacity,
					'storage' => 0
				]);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function unlockBuilding(int $buildingId, int $userId) {
		$building = $this->findAllBuildings()->get($buildingId);
		$land = $this->findPlayerLand($userId)->fetch();
		$buildingExist = $this->findAllPlayerBuildings($userId)->where('buildings_id', $buildingId)->fetch();
		if (!$buildingExist) {
			$this->findAllPlayerBuildings($userId)->insert([
				'user_id' => $userId,
				'buildings_id' => $buildingId,
				'player_land_id' => $land->id,
				'level' => 0,
				'capacity' => $building->base_capacity,
				'storage' => 0,
			]);
		}
	}

	public function upgradeBuilding(int $bId, ?int $userId = null) {
		$building = $this->getBuilding($bId)->fetch();
		if ($building) {
			$newLevel = $building->level + 1;
			if (!$building->income || $building->income <= 0) {
				$this->getBuilding($bId)->update([
					'level' => $newLevel,
					'capacity' => $this->getBuildingIncome($building->buildings->base_capacity, $newLevel)
				]);
			} else {
				$newIncome = $this->getBuildingIncome($building->buildings->base_income, $newLevel);
				$this->getBuilding($bId)->update([
					'income' => $newIncome,
					'level' => $newLevel,
					'capacity' => $this->getBuildingIncome($building->buildings->base_capacity, $newLevel)
				]);
				$incomeType = $this->getIncomeType($building->buildings->name);
				if ($incomeType != '') {
					$incomeAdd = $newIncome - $building->income;
					$this->findPlayerIncome($userId)->update([
						$incomeType . '+=' => $incomeAdd
					]);
				}
			}
			return true;
		} else {
			return false;
		}
	}

	public function demolishBuilding(int $bId, ?int $userId = null) {
		$building = $this->getBuilding($bId)->fetch();
		if ($building && isset($building->income)) {
			$incomeType = $this->getIncomeType($building->buildings->name);
			if ($incomeType != '') {
				$this->findPlayerIncome($userId)->update([
					$incomeType . '-=' => $building->income
				]);
			}
			$this->findPlayerLand($userId)->update([
				'free_slots+=' => 1
			]);
			$this->getBuilding($bId)->delete();
			return true;
		} else {
			return false;
		}
	}

	public function getLandPrice()
	{
		return $this->newLandPrice;
	}

	public function getLandUpgradeCost(int $level)
	{
		return (int)round($this->baseUpgradeCost * pow($level, 2), -2);
	}

	public function getLandUpgradeTime(int $level)
	{
		// return (int)round($this->baseUpgradeTime * pow($level, 2), -2);
		return (int)round($this->baseUpgradeTime * pow(($level), 1.05), 0);
	}

	public function getIncomeType(string $buildingName)
	{
		$incomeType = '';
			switch ($buildingName) {
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
			return $incomeType;
	}

	// Building income = baseIncome + round(baseIncome * ((level-1)/2)^1.05)
	/**
	 * getBuildingIncome
	 *
	 * @param integer $baseIncome
	 * @param integer $level
	 * @return float
	 */
	public function getBuildingIncome(int $baseIncome = 0, int $level = 1): float
	{
		return $baseIncome + round($baseIncome * pow(($level - 1) / 2, 1.02));
	}

	public function getBuildingCapacity(int $capacity = 0, int $level = 1)
	{
		return $capacity + round($capacity * pow(($level - 1) / 2, 1.02) * 1.25);
	}
}
