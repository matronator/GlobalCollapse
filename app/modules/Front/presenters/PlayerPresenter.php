<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class PlayerPresenter extends BasePresenter
{

	private $userRepository;

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

	public function renderDetail(?string $username = null) {
		if (!$username) {
			$this->redirect('Default:default');
		}
		$otherPlayer = $this->userRepository->getUserByName($username);
		if ($otherPlayer) {
			$this->template->otherPlayer = $otherPlayer;
		}
	}
}
