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
		}
	}

	public function actionBuyLand() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$cost = $this->buildingsRepository->getLandPrice();
		$playerMoney = $player->money;
		if ($playerMoney >= $cost && !$this->buildingsRepository->findPlayerLand($player->id)->fetch()) {
			$this->buildingsRepository->buyLand($player->id);
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
				$this->flashMessage('Building bought!', 'success');
				$this->redirect('Buildings:default');
			} else {
				$this->flashMessage('Not enough money!', 'danger');
				$this->redirect('Buildings:default');
			}
		}
	}
}
