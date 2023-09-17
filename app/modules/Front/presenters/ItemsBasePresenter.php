<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\InventoryRepository;
use App\Model\ItemsRepository;
use App\Model\MarketRepository;

abstract class ItemsBasePresenter extends GamePresenter
{
    /** @var InventoryRepository */
    protected $inventoryRepository;

    /** @var ItemsRepository */
    protected $itemsRepository;

    /** @var MarketRepository */
    public $marketRepository;

    protected $inventory;

    protected $playerBody;

    protected $market;

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

	public function startup()
	{
        parent::startup();

        $this->inventory = $this->inventoryRepository->findByUser($this->player->id)->fetch();
        if (!$this->inventory) {
            $this->inventory = $this->inventoryRepository->createInventory($this->player->id);
        }

        $this->playerBody = $this->inventoryRepository->findBodyByPlayerId($this->player->id)->fetch();
        if (!$this->playerBody) {
            $this->playerBody = $this->inventoryRepository->createBody($this->player->id);
        }

        $this->market = $this->marketRepository->getMarketByPlayerLevel($this->player->player_stats->level);
	}

    public function handleMoveItem(?int $startSlot, ?int $endSlot)
    {
        if ($startSlot === null || $endSlot === null) {
            $this->flashMessage($this->translator->translate('general.messages.danger.itemOrSlotNotSpecified'), 'danger');
            return;
        }

        $inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $startSlot)->fetch();
        if (!$inventoryItem) {
            $this->flashMessage($this->translator->translate('general.messages.danger.itemNotFoundInInventory'), 'danger');
            return;
        }

        $this->inventoryRepository->moveItem($this->inventory->id, $startSlot, $endSlot);

        $this->template->playerBody = $this->playerBody;
        $this->template->inventory = $this->inventory;

        $this->redrawControl('inventoryWrapper');
        $this->redrawControl('inventory');
        $this->redrawControl('playerBody');
    }

    public function getMarketSellCost(int $itemId): int
    {
        return $this->marketRepository->getMarketSellPrice($itemId, $this->market->id);
    }
}
