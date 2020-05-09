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
		$playerLand = $this->buildingsRepository->findPlayerLand($player->id)->fetch();
		$this->template->land = $playerLand;
		if (!$playerLand) {
			$this->template->emptyLand = $this->buildingsRepository->getLandPrice();
		}
	}

	public function actionBuyLand($playerMoney, $cost) {
		if ($playerMoney >= $cost && !$this->buildingsRepository->findPlayerLand($this->user->getIdentity()->id)->fetch()) {
			$this->buildingsRepository->buyLand($this->user->getIdentity()->id);
			$this->flashMessage('Land bought!', 'success');
			$this->redirect('Buildings:default');
		} else {
			$this->flashMessage('Not enough money!', 'danger');
			$this->redirect('Buildings:default');
		}
	}
}
