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
  private $buildingsRepository;

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
		$playerLand = $this->getLand($player->id);
		$this->template->land = $playerLand;
		if (!$playerLand) {
			$this->template->emptyLand = $this->buildingsRepository->findAll()->where('type', 'land')->fetch();
		}
	}

	public function actionBuyLand($playerMoney, $cost) {
		if ($playerMoney >= $cost) {
			$this->buildingsRepository->findPlayerBuildings($this->user->getIdentity()->id)->insert([
				'user_id' => $this->user->getIdentity()->id,
				'buildings_id' => 1
			]);
			$this->flashMessage('Land bought!', 'success');
			$this->redirect('this');
		} else {
			$this->flashMessage('Not enough money!', 'danger');
			$this->redirect('this');
		}
	}

	private function getLand(int $pId) {
		return $this->buildingsRepository->findPlayerLand($pId)->fetchAll();
	}
}
