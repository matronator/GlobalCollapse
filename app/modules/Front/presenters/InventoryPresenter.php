<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\AssaultsRepository;
use App\Model\InventoryRepository;
use App\Model\ItemsRepository;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class InventoryPresenter extends GamePresenter
{
	/** @var Model\InventoryRepository */
	private $inventoryRepository;

	/** @var Model\ItemsRepository */
	private $itemsRepository;

	public function __construct(
		InventoryRepository $inventoryRepository,
		ItemsRepository $itemsRepository
	)
	{
		parent::__construct();
		$this->inventoryRepository = $inventoryRepository;
		$this->itemsRepository = $itemsRepository;
	}

	protected function startup()
	{
		parent::startup();
	}

	public function renderDefault()
	{
		$inventory = $this->inventoryRepository->findByUser($this->user->getIdentity()->id)->fetch();
		if (!$inventory) {
			$inventory = $this->inventoryRepository->createInventory($this->user->getIdentity()->id);
		}
		$inventorySlots = [];
		for ($i = 0; $i < InventoryRepository::BASE_HEIGHT * InventoryRepository::BASE_WIDTH; $i++) {
			$inventorySlots[$i] = null;
		}

		$inventoryItems = $this->inventoryRepository->findAllItems($inventory->id)->fetchAll();
		foreach ($inventoryItems as $item) {
			$inventorySlots[$item->slot] = $item;
		}

		$this->template->player = $this->_player;
		$this->template->inventory = $inventorySlots;
	}
}
