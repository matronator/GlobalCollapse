<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\Entity\PlayerBody;
use App\Model\InventoryRepository;
use App\Model\ItemsRepository;
use Nette\Database\Table\ActiveRow;
use Tracy\Debugger;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class InventoryPresenter extends ItemsBasePresenter
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
			return;
		}

		$inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $slot)->fetch();
		if (!$inventoryItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
			return;
		}

		$item = $inventoryItem->ref('item');
		if (!$item) {
			$this->flashMessage('Item not found!', 'danger');
			return;
		}

		if (!in_array($item->subtype, PlayerBody::ALLOWED_ITEMS[$bodySlot][$item->type])) {
			$this->flashMessage('Item cannot be equipped in this slot!', 'danger');
			return;
		}

		if ($bodySlot === 'face' && $item->subtype === 'headgear' && $this->playerBody->head) {
			$this->flashMessage('You can\'t equip headgear while wearing a helmet.', 'warning');
			return;
        }
		if ($bodySlot === 'head' && $item->subtype === 'helmet' && $this->playerBody->face && $this->playerBody->ref('items', 'face')->subtype === 'headgear') {
			$this->flashMessage('You can\'t equip a helmet while wearing a headgear.', 'warning');
			return;
		}

		$this->inventoryRepository->equipItem($this->inventory->id, $item->id, $bodySlot, $slot, $this->_player->id);

        $this->refreshTemplate();
    }

	public function handleUnequipItem(?string $bodySlot, ?int $slot)
	{
		if ($bodySlot === null || $slot === null) {
			$this->flashMessage('Item or slot not specified!', 'danger');
			return;
		}

		$inventoryItem = $this->inventoryRepository->findInventoryItem($this->inventory->id, $slot)->fetch();
		if ($inventoryItem) {
			$this->flashMessage('Slot is not empty!', 'danger');
			return;
		}

		if (!$this->playerBody->$bodySlot) {
			$this->flashMessage('Slot is empty!', 'danger');
			return;
		}

		$this->inventoryRepository->unequipItem($this->inventory->id, $bodySlot, $slot, $this->_player->id);

        $this->refreshTemplate();
    }

	public function getEquippedItem(int $itemId)
	{
		$equippedItem = $this->itemsRepository->get($itemId);
		if (!$equippedItem) {
			$this->flashMessage('Item not found in inventory!', 'danger');
			return;
		}

		return $equippedItem;
	}

    private function refreshTemplate(): void
    {
        $this->playerBody = $this->inventoryRepository->findBodyByPlayerId($this->_player->id)->fetch();
        $this->template->playerBody = $this->playerBody;
        $this->template->inventory = $this->inventory;

        $this->flashMessage('Item equipped!', 'success');
        $this->redrawControl('inventoryWrapper');
        $this->redrawControl('inventory');
        $this->redrawControl('playerBody');
    }
}
