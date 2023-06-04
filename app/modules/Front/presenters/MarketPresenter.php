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
		$this->template->market = $this->market;
        $this->template->inventoryHasSpace = $this->inventoryRepository->checkInventoryHasSpace($this->_player->id);

		$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
		$this->template->imagesDir = ItemsRepository::IMAGES_DIR;

		$marketItems = $this->marketRepository->findAllItemsInMarket($this->market->id);
        $market = [];
        foreach ($marketItems as $item) {
            $market[$item->market_slot][] = $item;
        }
		$this->template->marketItems = $market;
	}

    public function handleBuyItem(?int $itemId)
    {
        if ($itemId === null) {
            $this->flashMessage('Item not specified!', 'danger');
            return;
        }

        $marketItem = $this->marketRepository->findAllMarketItems()->get($itemId);
        if (!$marketItem || $marketItem->market_id !== $this->market->id) {
            $this->flashMessage('Item not found in market!', 'danger');
            return;
        }

        if ($this->_player->money < $marketItem->item->cost) {
            $this->flashMessage($this->translator->trans('general.messages.danger.notEnoughMoney'), 'danger');
            return;
        }

        if ($this->_player->player_stats->level < $marketItem->item->unlock_at) {
            $this->flashMessage($this->translator->trans('general.messages.warning.itemLowLevel', ['level' => $marketItem->item->unlock_at]), 'danger');
            return;
        }

        if (!$this->marketRepository->buyItem($marketItem, $this->_player->id)) {
            $this->flashMessage($this->translator->trans('general.messages.danger.somethingWentWrong'), 'danger');
            return;
        }

        $this->template->playerBody = $this->playerBody;
        $this->template->inventory = $this->inventory;

        $this->redrawControl('inventoryWrapper');
        $this->redrawControl('inventory');
        $this->redrawControl('playerBody');
        $this->redrawControl('wrapper');
        $this->redrawControl('sidebar-stats');
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
