<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\AssaultsRepository;
use App\Model\Entity\PlayerBody;
use App\Model\InventoryRepository;
use App\Model\ItemsRepository;
use Tracy\Debugger;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class InventoryPresenter extends GamePresenter
{
	/** @var Model\InventoryRepository */
	private $inventoryRepository;

	/** @var Model\ItemsRepository */
	private $itemsRepository;

	private $inventory;

	private $playerBody;

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

		if (!Debugger::isEnabled() || Debugger::getStrategy() === Debugger::PRODUCTION) {
			$this->flashMessage('Inventory is still under construction.', 'warning');
			$this->redirect('Default:default');
		}

		$this->inventory = $this->inventoryRepository->findByUser($this->_player->id)->fetch();
		if (!$this->inventory) {
			$this->inventory = $this->inventoryRepository->createInventory($this->_player->id);
		}

		$this->playerBody = $this->inventoryRepository->findBodyByPlayerId($this->_player->id)->fetch();
		if (!$this->playerBody) {
			$this->playerBody = $this->inventoryRepository->createBody($this->_player->id);
		}
	}

	public function renderDefault()
	{
		$inventorySlots = [];
		for ($i = 0; $i < InventoryRepository::BASE_HEIGHT * InventoryRepository::BASE_WIDTH; $i++) {
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
	}

	public function handleEquipItem(?int $itemId, ?string $bodySlot, ?int $slot)
	{
		if (!$itemId || !$bodySlot) {
			$this->flashMessage('Item or slot not specified!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		$inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $slot)->fetch();
		if (!$inventoryItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		$item = $inventoryItem->ref('item');

		if (!in_array($item->type, PlayerBody::ALLOWED_ITEMS[$bodySlot])) {
			$this->flashMessage('Item cannot be equipped in this slot!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		if (!in_array($item->subtype, PlayerBody::ALLOWED_ITEMS[$bodySlot][$item->type])) {
			$this->flashMessage('Item cannot be equipped in this slot!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		$this->inventoryRepository->equipItem($this->inventory->id, $item->id, $bodySlot, $slot, $this->_player->id);

		$this->flashMessage('Item equipped!', 'success');
		$this->redirect('default');
	}

	public function handleUnequipItem(?string $bodySlot, ?int $slot)
	{
		if ($bodySlot === null || $slot === null) {
			$this->flashMessage('Item or slot not specified!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		$inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $slot)->fetch();
		if ($inventoryItem) {
			$this->flashMessage('Slot is not empty!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		if (!$this->playerBody->$bodySlot) {
			$this->flashMessage('Slot is empty!', 'danger');
			$this->redrawControl('inventory');
			$this->redrawControl('js');
		}

		$this->inventoryRepository->unequipItem($this->inventory->id, $bodySlot, $slot, $this->_player->id);

		$this->flashMessage('Item equipped!', 'success');
		$this->redirect('default');
		$this->redrawControl('js');
	}

	public function handleMoveItem(?int $startSlot, ?int $endSlot)
	{
		if ($startSlot === null || $endSlot === null) {
			$this->flashMessage('Item or slot not specified!', 'danger');
			$this->redrawControl('inventory');
		}

		$inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $startSlot)->fetch();
		if (!$inventoryItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
			$this->redrawControl('inventory');
		}

		$this->inventoryRepository->moveItem($this->inventory->id, $startSlot, $endSlot);

		$this->flashMessage('Item moved!', 'success');
		// $this->redirect('default');
		$this->redrawControl('inventory');
		$this->redrawControl('js');
	}

	public function getEquippedItem(int $itemId)
	{
		$equippedItem = $this->itemsRepository->get($itemId);
		if (!$equippedItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
		}

		return $equippedItem;
	}
}
