<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use DateTime;
use Nette\Application\UI\Form;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class TrainingPresenter extends GamePresenter
{

	private $userRepository;
	private $drugsRepository;

	public function __construct(
		UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDarknet()
	{

	}

	private function updateStats() {
    $newStats = $this->userRepository->getUser($this->player->id);
		$this->player->power = $newStats->power;
		$this->player->speed = $newStats->speed;
		$this->player->health = $newStats->health;
		$this->player->money = $newStats->money;
		$this->player->skillpoints = $newStats->skillpoints;
		$this->player->energy = $newStats->energy;
		$this->player->energy_max = $newStats->energy_max;
  }
}
