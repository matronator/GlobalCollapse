<?php

namespace App\Model;

use DateTime;
use Nette;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

class UnlockablesRepository
{
	public const TYPE_LAND_LEVEL = 'land_level';
	public const TYPE_LEVEL = 'level';

	public const UNLOCKS_BUILDING = 'building';
	public const UNLOCKS_MAX_ENERGY = 'max_energy';

	/** @var Nette\Database\Explorer */
	private $database;
	private BuildingsRepository $buildingsRepository;
	private UserRepository $userRepository;

	public function __construct(Nette\Database\Explorer $database, BuildingsRepository $buildingsRepository, UserRepository $userRepository)
	{
		$this->database = $database;
		$this->buildingsRepository = $buildingsRepository;
		$this->userRepository = $userRepository;
	}

	public function findAll()
	{
		return $this->database->table('unlockables');
	}

	public function findPlayerUnlocked(int $userId): Selection
	{
		return $this->database->table('player_unlocked')->where('user_id', $userId);
	}

    public function getPossibleToUnlock(int $level): Selection
    {
        return $this->findAll()->where('unlock_at <= ?', $level);
    }

	public function checkUnlockables($user, $land)
	{
		$toUnlock = [];
		$landLevel = isset($land->level) ? $land->level : 0;
		$types = [
			self::TYPE_LEVEL => $user->player_stats->level,
			self::TYPE_LAND_LEVEL => $landLevel,
		];
		foreach ($types as $type => $level) {
			array_push($toUnlock, ...$this->getUnlockablesByType($level, $user->id, $type));
		}
		$this->unlockItems($toUnlock, $user->id);
	}

	public function getUnlockablesByType(int $level, int $userId, string $type): array
	{
		$unlockables = $this->getPossibleToUnlock($level)->where('type', $type)->fetchAll();
		$toUnlock = [];
		foreach ($unlockables as $unlockable) {
			$isUnlocked = $this->findPlayerUnlocked($userId)->where('unlockables_id', $unlockable->id)->fetchAll();
			if (count($isUnlocked) <= 0) {
				if ($level >= $unlockable->unlock_at) {
					$toUnlock[] = $unlockable;
				}
			}
		}

		return $toUnlock;
	}

	public function unlockItems(array $toUnlock, int $userId)
	{
		foreach ($toUnlock as $item) {
			$this->findPlayerUnlocked($userId)->insert([
				'user_id' => $userId,
				'unlockables_id' => $item->id,
				'quantity' => 1,
				'unlocked_at' => new DateTime,
			]);

			switch ($item->unlocks) {
				case self::UNLOCKS_BUILDING:
					$this->buildingsRepository->unlockBuilding($item->buildings_id, $userId);
					break;
				case self::UNLOCKS_MAX_ENERGY:
					$this->userRepository->increaseMaxEnergy($userId, $item->amount);
					break;
			}
		}
	}
}
