<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;
use App\Model\UserRepository;
use App\Model\BuildingsRepository;
use App\Model\DrugsRepository;
use Timezones;

final class BuildingsPresenter extends GamePresenter
{
	private $userRepository;
  public $buildingsRepository;
	private $drugsRepository;

	public function __construct(
		UserRepository $userRepository,
		BuildingsRepository $buildingsRepository,
		DrugsRepository $drugsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->buildingsRepository = $buildingsRepository;
		$this->drugsRepository = $drugsRepository;
	}

	protected function startup()
	{
		parent::startup();
	}

  	public function renderDefault() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$playerLand = $this->buildingsRepository->findPlayerLand($player->id)->fetch();
		$this->template->land = $playerLand;
		if (!$playerLand) {
			$this->template->emptyLand = $this->buildingsRepository->getLandPrice();
		} else {
			$playerBuildings = $this->buildingsRepository->findPlayerBuildings($player->id);
			$this->template->playerBuildings = $playerBuildings;
			if ($playerBuildings->count() >= 5) {
				$fullBuildings = $this->buildingsRepository->findPlayerBuildings($player->id)->where('storage > 0');
				if ($fullBuildings->count() > 0) {
					$this->template->collectAll = true;
				}
			}

			$unlockedBuildings = $this->buildingsRepository->findAllUnlocked($player->id)->order('buildings.price DESC');
			$this->template->unlockedBuildings = $unlockedBuildings;
			$playerIncome = $this->buildingsRepository->findPlayerIncome($player->id)->fetch();
			$this->template->playerIncome = $playerIncome;

			$drugs = $this->drugsRepository->findAll()->fetchAll();
			$this->template->drugs = $drugs;
			$drugsInventory = $this->drugsRepository->findDrugInventory($player->id)->order('drugs_id', 'ASC')->fetchAll();
			$playerDrugs = [];
			if (count($drugsInventory) > 0) {
				foreach ($drugsInventory as $drug) {
					$playerDrugs[$drug->drugs->name] = $drug->quantity;
				}
			} else {
				foreach ($drugs as $drug) {
					$playerDrugs[$drug->name] = 0;
				}
			}
			$this->template->playerDrugs = $playerDrugs;

			$this->template->landUpgradeCost = $this->buildingsRepository->getLandUpgradeCost($playerLand->level);
			$this->template->landSlotsNext = $this->buildingsRepository->getLandSlotGain($playerLand->level);
			$this->template->landUpgradeTime = round($this->buildingsRepository->getLandUpgradeTime($playerLand->level) / 3600, 0);
			$isUpgrading = $playerLand->is_upgrading;
			$this->template->isUpgrading = $isUpgrading;
			if ($isUpgrading > 0) {
				$upgradeUntil = $playerLand->upgrade_end;
				$now = new DateTime();
				$diff = $upgradeUntil->getTimestamp() - $now->getTimestamp();
				if ($diff > 0) {
					$s = $diff % 60;
					$m = $diff / 60 % 60;
					$h = $diff / 3600 % 60;
					$this->template->hours = $h > 9 ? $h : '0'.$h;
					$this->template->minutes = $m > 9 ? $m : '0'.$m;
					$this->template->seconds = $s > 9 ? $s : '0'.$s;
					$this->template->upgradeUntil = Timezones::getUserTime($upgradeUntil, $this->userPrefs->timezone, $this->userPrefs->dst);
				} else {
					$this->buildingsRepository->upgradeLand($player->id);
					$isUpgrading = 0;
					$this->flashMessage('Land upgraded!', 'success');
					$this->redrawControl('playerIncome');
					$this->redrawControl('playerStash');
					$this->redrawControl('buildings');
					$this->redrawControl('land-card');
					$this->redrawControl('sidebar-stats');
					$this->redrawControl('landUpgradeProgress');
				}
			}
			if (isset($playerIncome->last_collection)) {
				$this->template->lastCollection = Timezones::getUserTime($playerIncome->last_collection, $this->userPrefs->timezone, $this->userPrefs->dst);
				$updated = $playerIncome->last_collection;
				$now = new DateTime();
				$diff = abs($updated->getTimestamp() - $now->getTimestamp());
				if ($diff < 3600) {
					$this->template->timeAgo = round($diff / 60) . ' minutes';
				} else if ($diff <= 5400) {
					$this->template->timeAgo = round($diff / 3600) . ' hour';
				} else {
					$this->template->timeAgo = round($diff / 3600) . ' hours';
				}
			} else {
				$this->template->noLastCollection = true;
				$testIncome = $this->buildingsRepository->findPlayerIncome(1)->fetch();
				$updated = $testIncome->last_collection;
				$now = new DateTime();
				$diff = abs($updated->getTimestamp() - $now->getTimestamp());
				if ($diff < 3600) {
					$this->template->timeAgo = round($diff / 60) . ' minutes';
				} else if ($diff <= 5400) {
					$this->template->timeAgo = round($diff / 3600) . ' hour';
				} else {
					$this->template->timeAgo = round($diff / 3600) . ' hours';
				}
			}
		}
	}

	public function renderLands() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$playerLand = $this->buildingsRepository->findPlayerLand($player->id)->fetch();
		$this->template->land = $playerLand;
	}

	public function handleUpgradeLand() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$playerLand = $this->buildingsRepository->findPlayerLand($player->id)->fetch();
		if (isset($playerLand->level) && $playerLand->level >= 1 && !$playerLand->is_upgrading) {
			$cost = $this->buildingsRepository->getLandUpgradeCost($playerLand->level);
			$playerMoney = $player->money;
			if ($playerMoney >= $cost) {
				$this->buildingsRepository->startLandUpgrade($player->id);
				$this->userRepository->addMoney($player->id, -$cost);
				$this->flashMessage($this->translate('general.messages.success.landUpgradeStart'), 'success');
				$this->redrawControl('playerIncome');
				$this->redrawControl('playerStash');
				$this->redrawControl('buildings');
				$this->redrawControl('land-card');
				$this->redrawControl('sidebar-stats');
			} else {
				$this->flashMessage($this->translate('general.messages.danger.notEnoughMoney'), 'danger');
				$this->redrawControl('land-card');
			}
		} else {
			$this->redirect('Buildings:default');
		}
	}

	public function actionBuyLand() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$cost = $this->buildingsRepository->getLandPrice();
		$playerMoney = $player->money;
		if ($playerMoney >= $cost && !$this->buildingsRepository->findPlayerLand($player->id)->fetch()) {
			$this->buildingsRepository->buyLand($player->id);
			$this->userRepository->addMoney($player->id, -$cost);
			$this->flashMessage($this->translate('general.messages.success.landBought'), 'success');
			$this->redirect('Buildings:default');
		} else {
			$this->flashMessage($this->translate('general.messages.danger.notEnoughMoney'), 'danger');
			$this->redirect('Buildings:default');
		}
	}

	public function handleBuyBuilding(int $b) {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$building = $this->buildingsRepository->getBuilding($b)->fetch();
		if (!$building) {
			$this->flashMessage($this->translate('general.messages.danger.buildingNotFound'), 'danger');
			$this->redirect('Buildings:default');
		}
		if (isset($building->user_id) && $building->user_id === $player->id && $building->level === 0) {
			$playerMoney = $player->money;
			$cost = $building->buildings->price;
			if ($playerMoney >= $cost) {
				$this->buildingsRepository->buyBuilding($player->id, $building->buildings_id, $b);
				$this->userRepository->addMoney($player->id, -$cost);
				$this->flashMessage($this->translate('general.messages.success.buildingBought'), 'success');
				$this->redrawControl('playerIncome');
				$this->redrawControl('playerStash');
				$this->redrawControl('buildings');
				$this->redrawControl('sidebar-stats');
			} else {
				$this->flashMessage($this->translate('general.messages.danger.notEnoughMoney'), 'danger');
				$this->redrawControl('buildings');
			}
		}
	}

	public function handleCollect(int $b) {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$building = $this->buildingsRepository->getBuilding($b)->fetch();
		if (!$building) {
			$this->flashMessage($this->translate('general.messages.danger.buildingNotFound'), 'danger');
			$this->redirect('Buildings:default');
		}
		if ($building->user_id === $player->id) {
			if ($building->storage > 0) {
				$drugId = $building->buildings->drugs_id;
				$this->drugsRepository->buyDrugs($player->id, $drugId, $building->storage);
				$this->buildingsRepository->getBuilding($building->id)->update([
					'storage' => 0
				]);
				$this->flashMessage($this->translate('general.messages.success.buildingCollected'), 'success');
				// $this->redirect('Buildings:default');
				$this->redrawControl('playerStash');
				$this->redrawControl('buildings');
				$this->redrawControl('sidebar-stats');
			} else {
				// $this->redrawControl('buildings');
				$this->redirect('Buildings:default');
			}
		} else {
			$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
			$this->redirect('Buildings:default');
		}
	}

	public function handleCollectAll() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$buildings = $this->buildingsRepository->findPlayerBuildings($player->id)->where('storage > 0')->where('user_id', $player->id);
		$count = 0;
		foreach ($buildings as $building) {
			$drugId = $building->buildings->drugs_id;
			$this->drugsRepository->buyDrugs($player->id, $drugId, $building->storage);
			$building->update(['storage' => 0]);
			$count++;
		}
		if ($count > 0) {
			$this->flashMessage($this->translate('general.messages.success.allBuildingCollected'), 'success');
			$this->redrawControl('playerStash');
			$this->redrawControl('buildings');
			$this->redrawControl('sidebar-stats');
		}
	}

	public function handleUpgrade(int $b) {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$building = $this->buildingsRepository->getBuilding($b)->fetch();
		if (isset($building->user_id) && $building->user_id === $player->id) {
			$playerMoney = $player->money;
			$cost = $this->getUpgradeCost($building->buildings->price, $building->level);
			if (!$building->buildings->max_level || $building->level < $building->buildings->max_level) {
				if ($playerMoney >= $cost) {
					if ($this->buildingsRepository->upgradeBuilding($b, $player->id)) {
						$this->userRepository->addMoney($player->id, -$cost);
						$this->flashMessage($this->translate('general.messages.success.buildingUpgraded'), 'success');
					} else {
						$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
					}
				} else {
					$this->flashMessage($this->translate('general.messages.danger.notEnoughMoney'), 'danger');
				}
			} else {
				$this->flashMessage('This building is at maximum level or is not upgradable!', 'danger');
			}
			$this->redrawControl('playerIncome');
			$this->redrawControl('buildings');
			$this->redrawControl('sidebar-stats');
		} else {
			$this->redirect('Buildings:default');
		}
	}

	public function handleDemolish(int $b) {
		$building = $this->buildingsRepository->getBuilding($b)->fetch();
		if (!$building) {
			$this->flashMessage($this->translate('general.messages.danger.buildingNotFound'), 'danger');
			$this->redirect('Buildings:default');
		}
		if ($building->user_id === $this->user->getIdentity()->id) {
			if ($this->buildingsRepository->demolishBuilding($b, $this->user->getIdentity()->id)) {
				$this->flashMessage('Building demolished!', 'success');
			} else {
				$this->flashMessage('Building not found!', 'danger');
			}
			$this->redrawControl('playerIncome');
			$this->redrawControl('buildings');
			$this->redrawControl('sidebar-stats');
		} else {
			$this->redirect('Buildings:default');
		}
	}

	public function getUpgradeCost(int $basePrice = 0, int $level = 1): int
	{
		return (int) round(($basePrice * pow($level, 1.05)) / 2, -1);
	}
}
