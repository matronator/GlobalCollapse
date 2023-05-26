<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\Entity\PlayerBody;
use App\Model\InventoryRepository;
use App\Model\ItemsRepository;
use App\Model\MarketRepository;
use Tracy\Debugger;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class MarketPresenter extends GamePresenter
{
	/** @var Model\InventoryRepository */
	private $inventoryRepository;
	
	/** @var Model\ItemsRepository */
	private $itemsRepository;
	
	/** @var Model\MarketRepository */
	private $marketRepository;
	
	private $inventory;
	
	private $playerBody;

	private $market;
	
	public function __construct(
		InventoryRepository $inventoryRepository,
		ItemsRepository $itemsRepository,
		MarketRepository $marketRepository
	)
	{
		parent::__construct();
		$this->inventoryRepository = $inventoryRepository;
		$this->itemsRepository = $itemsRepository;
		$this->marketRepository = $marketRepository;
	}
		
	protected function startup()
	{
		parent::startup();
		
		$this->inventory = $this->inventoryRepository->findByUser($this->_player->id)->fetch();
		if (!$this->inventory) {
			$this->inventory = $this->inventoryRepository->createInventory($this->_player->id);
		}
		
		$this->playerBody = $this->inventoryRepository->findBodyByPlayerId($this->_player->id)->fetch();
		if (!$this->playerBody) {
			$this->playerBody = $this->inventoryRepository->createBody($this->_player->id);
		}

		$this->market = $this->marketRepository->getMarketByPlayerLevel($this->_player->player_stats->level);
	}
	
	public function renderDefault()
	{
		$inventorySlots = [];
		for ($i = 0; $i < $this->inventory->height * $this->inventory->width; $i++) {
			$inventorySlots[$i] = null;
		}
		
		$inventoryItems = $this->inventoryRepository->findAllInventoryItems($this->inventory->id)->fetchAll();
		foreach ($inventoryItems as $item) {
			$inventorySlots[$item->slot] = $item;
		}
		
		$this->template->playerBody = $this->playerBody;
		$this->template->player = $this->_player;
		$this->template->inventorySlots = $inventorySlots;
		$this->template->inventory = $this->inventory;
		
		$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
		$this->template->imagesDir = ItemsRepository::IMAGES_DIR;

		$this->template->market = $this->market;
		$marketItems = $this->marketRepository->findAllItemsInMarket($this->market->id);
		$this->template->marketItems = $marketItems;
	}

	public function handleMoveItem(?int $startSlot, ?int $endSlot)
	{
		if ($startSlot === null || $endSlot === null) {
			$this->flashMessage('Item or slot not specified!', 'danger');
			return;
		}
		
		$inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $startSlot)->fetch();
		if (!$inventoryItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
			return;
		}
		
		$this->inventoryRepository->moveItem($this->inventory->id, $startSlot, $endSlot);

		$this->template->playerBody = $this->playerBody;
		$this->template->inventory = $this->inventory;
		
		$this->redrawControl('inventoryWrapper');
		$this->redrawControl('inventory');
		$this->redrawControl('playerBody');
	}
}
