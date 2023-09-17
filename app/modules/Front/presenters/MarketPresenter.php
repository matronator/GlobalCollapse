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

final class MarketPresenter extends ItemsBasePresenter
{
	public function startup()
	{
		parent::startup();
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
		$this->template->player = $this->player;
		$this->template->inventorySlots = $inventorySlots;
		$this->template->inventory = $this->inventory;
		$this->template->market = $this->market;
        $this->template->inventoryHasSpace = $this->inventoryRepository->checkInventoryHasSpace($this->player->id);

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

        if ($this->player->money < $this->marketRepository->getItemPrice($marketItem)) {
            $this->flashMessage($this->translator->translate('general.messages.danger.notEnoughMoney'), 'danger');
            return;
        }

        if ($this->player->player_stats->level < $marketItem->item->unlock_at) {
            $this->flashMessage($this->translator->translate('general.messages.warning.itemLowLevel', ['level' => $marketItem->item->unlock_at]), 'danger');
            return;
        }

        if (!$this->marketRepository->buyItem($marketItem, $this->player->id)) {
            $this->flashMessage($this->translator->translate('general.messages.danger.somethingWentWrong'), 'danger');
            return;
        }

        $this->template->playerBody = $this->playerBody;
        $this->template->inventory = $this->inventory;

        $this->redrawControl('inventoryWrapper');
        $this->redrawControl('inventory');
        $this->redrawControl('playerBody');
        $this->redrawControl('wrapper');
        $this->redrawControl('flashes');
        $this->redrawControl('sidebar-stats');
    }

    public function handleSellItem(?int $slotId)
    {
        if (!$slotId) {
            $this->flashMessage($this->translator->translate('general.messages.danger.itemOrSlotNotSpecified'), 'danger');
            $this->redrawControl('wrapper');
            $this->redrawControl('flashes');
            return;
        }

        $inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $slotId)->fetch();
        if (!$inventoryItem) {
            $this->flashMessage($this->translator->translate('general.messages.danger.itemNotFoundInInventory'), 'danger');
            $this->redrawControl('wrapper');
            $this->redrawControl('flashes');
            return;
        }

        $this->marketRepository->sellItem($inventoryItem, $this->market->id);

        $this->flashMessage($this->translator->translate('general.messages.success.itemSold'), 'success');
        $this->redrawControl('inventoryWrapper');
        $this->redrawControl('inventory');
        $this->redrawControl('playerBody');
        $this->redrawControl('wrapper');
        $this->redrawControl('flashes');
        $this->redrawControl('sidebar-stats');
    }
}
