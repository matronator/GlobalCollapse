<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\AssaultsRepository;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class PlayerPresenter extends BasePresenter
{

	private $userRepository;

	/** @var Model\AssaultsRepository */
  private $assaultsRepository;

	public function __construct(
		UserRepository $userRepository,
		AssaultsRepository $assaultsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->assaultsRepository = $assaultsRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDetail(?string $user = null) {
		if (!$user) {
			$this->redirect('Default:default');
		} else {
			$otherPlayer = $this->userRepository->getUserByName($user);
			if ($otherPlayer) {
				$this->template->otherPlayer = $otherPlayer;
				$aStatsV = $this->assaultsRepository->findPlayerAssaultStats($otherPlayer->id)->fetch();
				$this->template->aStatsV = $aStatsV;
			} else {
				$this->error();
			}
		}
	}

	public function renderLeaderboard(int $page = 1) {
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);

			$usersRanked = $this->userRepository->findAll()->select('*')->order('player_stats.power DESC');
			$lastPage = 0;
			$ranked = $usersRanked->page($page, 20, $lastPage);
			$data = [];
			foreach ($ranked as $rankedPlayer) {
				array_push($data, $rankedPlayer);
			}
			$this->template->user = $player;
			$this->template->data = $data;
			$this->template->page = $page;
			$this->template->itemsPerPage = 20;
			$this->template->lastPage = $lastPage;
		} else {
			$this->redirect('Default:default');
		}
	}
}
