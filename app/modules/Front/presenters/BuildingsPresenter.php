<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;
use App\Model\UserRepository;
use App\Model\BuildingsRepository;

final class BuildingsPresenter extends GamePresenter
{
	private $userRepository;
  public $buildingsRepository;

	public function __construct(
		UserRepository $userRepository,
		BuildingsRepository $buildingsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->buildingsRepository = $buildingsRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

  public function renderDefault() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$this->template->user = $player;
		$playerLand = $this->buildingsRepository->findPlayerLand($player->id)->fetch();
		$this->template->land = $playerLand;
		if (!$playerLand) {
			$this->template->emptyLand = $this->buildingsRepository->getLandPrice();
		} else {
			$playerBuildings = $this->buildingsRepository->findPlayerBuildings($player->id);
			$this->template->playerBuildings = $playerBuildings;
			$unlockedBuildings = $this->buildingsRepository->findAllUnlocked($player->id);
			$this->template->unlockedBuildings = $unlockedBuildings;
			$playerIncome = $this->buildingsRepository->findPlayerIncome($player->id)->fetch();
			$this->template->playerIncome = $playerIncome;
		}
	}

	public function actionBuyLand() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$cost = $this->buildingsRepository->getLandPrice();
		$playerMoney = $player->money;
		if ($playerMoney >= $cost && !$this->buildingsRepository->findPlayerLand($player->id)->fetch()) {
			$this->buildingsRepository->buyLand($player->id);
			$this->userRepository->getUser($player->id)->update([
				'money-=' => $cost
			]);
			$this->flashMessage('Land bought!', 'success');
			$this->redirect('Buildings:default');
		} else {
			$this->flashMessage('Not enough money!', 'danger');
			$this->redirect('Buildings:default');
		}
	}

	public function actionBuyBuilding(int $b) {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$building = $this->buildingsRepository->findAllUnlocked($player->id)->where('buildings_id', $b)->fetch();
		if ($building) {
			$playerMoney = $player->money;
			$cost = $building->buildings->price;
			if ($playerMoney >= $cost) {
				$this->buildingsRepository->buyBuilding($player->id, $building->buildings_id);
				$this->userRepository->getUser($player->id)->update([
					'money-=' => $cost
				]);
				$this->flashMessage('Building bought!', 'success');
				$this->redirect('Buildings:default');
			} else {
				$this->flashMessage('Not enough money!', 'danger');
				$this->redirect('Buildings:default');
			}
		}
	}

	public function actionUpgrade(int $b) {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$building = $this->buildingsRepository->getBuilding($b)->fetch();
		if ($building && $building->user_id === $player->id) {
			$playerMoney = $player->money;
			$cost = $this->getUpgradeCost($building->buildings->price, $building->level);
			if (!$building->buildings->max_level || $building->level < $building->buildings->max_level) {
				if ($playerMoney >= $cost) {
					if ($this->buildingsRepository->upgradeBuilding($b, $player->id)) {
						$this->userRepository->getUser($player->id)->update([
							'money-=' => $cost
						]);
						$this->flashMessage('Building bought!', 'success');
						$this->redirect('Buildings:default');
					} else {
						$this->flashMessage('Something fishy...', 'danger');
						$this->redirect('Buildings:default');
					}
				} else {
					$this->flashMessage('Not enough money!', 'danger');
					$this->redirect('Buildings:default');
				}
			} else {
				$this->flashMessage('This building is at maximum level or is not upgradable!', 'danger');
				$this->redirect('Buildings:default');
			}
		}
	}

	public function actionDemolish(int $b) {
		$building = $this->buildingsRepository->demolishBuilding($b, $this->user->getIdentity()->id);
		if ($building && $building->user_id === $this->user->getIdentity()->id) {
			$this->flashMessage('Building demolished!', 'success');
			$this->redirect('Buildings:default');
		} else {
			$this->flashMessage('Building not found!', 'danger');
			$this->redirect('Buildings:default');
		}
	}

	private function getUpgradeCost(int $basePrice = 0, int $level = 1) {
		return round(($basePrice * $level) / 3, -1);
	}
}
