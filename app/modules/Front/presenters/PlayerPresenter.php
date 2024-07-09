<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\AssaultsRepository;
use App\Model\InventoryRepository;
use App\Model\ItemsRepository;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class PlayerPresenter extends BasePresenter
{
	/** @var Model\AssaultsRepository */
 	private $assaultsRepository;

	/** @var Model\InventoryRepository */
 	private $inventoryRepository;

	/** @var Model\ItemsRepository */
 	private $itemsRepository;

	public function __construct(
		AssaultsRepository $assaultsRepository,
		InventoryRepository $inventoryRepository,
		ItemsRepository $itemsRepository
	)
	{
		parent::__construct();
		$this->assaultsRepository = $assaultsRepository;
		$this->inventoryRepository = $inventoryRepository;
		$this->itemsRepository = $itemsRepository;
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
				$playerBody = $this->inventoryRepository->findBodyByPlayerId($otherPlayer->id)->fetch();
				if (!$playerBody) {
					$playerBody = (object) ['head' => null, 'face' => null, 'body' => null, 'back' => null, 'shoulders' => null, 'legs' => null, 'feet' => null, 'melee' => null, 'ranged' => null, 'shield' => null];
				}
				$this->template->playerBody = $playerBody;
				$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
				$this->template->imagesDir = ItemsRepository::IMAGES_DIR;
			} else {
				$this->error();
			}
		}
	}

	public function getEquippedItem(int $itemId)
	{
		$equippedItem = $this->itemsRepository->get($itemId);
		if (!$equippedItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
			return false;
		}

		return $equippedItem;
	}

	public function renderLeaderboard(int $page = 1) {
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);

			$usersRanked = $this->userRepository->getLeaderboard();
			$lastPage = 0;
			$ranked = $usersRanked->page($page, 20, $lastPage);
			$data = [];
			foreach ($ranked as $rankedPlayer) {
				$data[] = $rankedPlayer;
			}
			$this->template->data = $data;
			$this->template->page = $page;
			$this->template->itemsPerPage = 20;
			$this->template->lastPage = $lastPage;
		} else {
			$this->redirect('Default:default');
		}
	}

    public function renderStatistics(string $user) {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Default:default');
        }

        $player = $this->userRepository->getUserByName($user);
    }
}
