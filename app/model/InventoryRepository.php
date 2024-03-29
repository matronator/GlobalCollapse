<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Item;
use Nette;
use Tracy\Debugger;

class InventoryRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    public const BASE_WIDTH = 4;
    public const BASE_HEIGHT = 3;

    public ItemsRepository $itemsRepository;

    public function __construct(Nette\Database\Explorer $database, ItemsRepository $itemsRepository)
    {
        $this->database = $database;
        $this->itemsRepository = $itemsRepository;
    }

    public function findAll()
    {
        return $this->database->table('player_inventory');
    }

    public function findByUser(int $userId): Nette\Database\Table\Selection
    {
        return $this->findAll()->where('user_id', $userId);
    }

    public function createInventory(int $userId)
    {
        if ($this->findByUser($userId)->count() > 0) {
            return;
        }

        return $this->findAll()->insert([
            'user_id' => $userId,
            'width' => self::BASE_WIDTH,
            'height' => self::BASE_HEIGHT,
            'level' => 1,
        ]);
    }

    public function findAllInventoryItems(int $inventoryId)
    {
        return $this->database->table('inventory_item')->where('player_inventory_id', $inventoryId);
    }

    public function findInventoryItem(int $inventoryId, int $slot)
    {
        return $this->findAllInventoryItems($inventoryId)->where('slot', $slot);
    }

    public function findAllBody()
    {
        return $this->database->table('player_body');
    }

    public function findBodyByPlayerId(int $playerId)
    {
        return $this->findAllBody()->where('user_id', $playerId);
    }

    public function findEquippedItem(int $playerId, string $bodySlot)
    {
        $body = $this->findBodyByPlayerId($playerId)->fetch();
        if (!$body) {
            return null;
        }

        return $this->database->table('items')->get($body->{$bodySlot});
    }

    public function createBody(int $userId)
    {
        if ($this->findBodyByPlayerId($userId)->fetch() !== null) {
            return $this->findBodyByPlayerId($userId)->fetch();
        }

        return $this->findAllBody()->insert([
            'user_id' => $userId,
            'head' => null,
            'face' => null,
            'body' => null,
            'shoulders' => null,
            'melee' => null,
            'ranged' => null,
            'shield' => null,
            'legs' => null,
            'feet' => null,
            'back' => null,
        ]);
    }

    public function findAllGearStats() {
        return $this->database->table('player_gear_stats');
    }

    public function findPlayerGearStats(int $userId)
    {
        return $this->findAllGearStats()->where('user_id', $userId);
    }

    public function checkSlotIsEmpty(int $userId, int $slot)
    {
        $inventory = $this->findAll()->where('user_id', $userId)->fetch();
        if (!$inventory) {
            return false;
        }

        $item = $this->findInventoryItem($inventory->id, $slot)->fetch();
        if (!$item) {
            return true;
        }

        return false;
    }

    public function findEmptySlot(int $userId): ?int
    {
        $inventory = $this->findAll()->where('user_id', $userId)->fetch();
        if (!$inventory) {
            return null;
        }

        $slots = $inventory->width * $inventory->height;

        for ($i = 0; $i < $slots; $i++) {
            $item = $this->findInventoryItem($inventory->id, $i)->fetch();
            if (!$item) {
                return $i;
            }
        }

        return null;
    }

    public function checkInventoryHasSpace(int $userId): bool
    {
        $inventory = $this->findAll()->where('user_id', $userId)->fetch();
        if (!$inventory) {
            return false;
        }

        $items = $this->findAllInventoryItems($inventory->id)->fetchAll();
        $itemsCount = count($items);
        $inventorySize = $inventory->width * $inventory->height;
        return $itemsCount < $inventorySize;
    }

    public function addGearStats(int $userId, array $stats) {
        $gearStats = $this->findPlayerGearStats($userId)->fetch();
        if (!$gearStats) {
            $this->findAllGearStats()->insert([
                'user_id' => $userId,
                'strength' => $stats['strength'] ?? 0,
                'stamina' => $stats['stamina'] ?? 0,
                'speed' => $stats['speed'] ?? 0,
                'attack' => $stats['attack'] ?? 0,
                'armor' => $stats['armor'] ?? 0,
                'energy_max' => $stats['energy_max'] ?? 0,
                'xp_boost' => $stats['xp_boost'] ?? 1,
            ]);
        } else {
            $gearStats->update([
                'strength+=' => $stats['strength'] ?? 0,
                'stamina+=' => $stats['stamina'] ?? 0,
                'speed+=' => $stats['speed'] ?? 0,
                'attack+=' => $stats['attack'] ?? 0,
                'armor+=' => $stats['armor'] ?? 0,
                'energy_max+=' => $stats['energy_max'] ?? 0,
                'xp_boost*=' => $stats['xp_boost'] ?? 1,
            ]);
        }
    }

    public function equipItem(int $inventoryId, int $itemId, string $bodySlot, int $slot, int $userId)
    {
        $inventoryItem = $this->findInventoryItem($inventoryId, $slot)->fetch();
        if (!$inventoryItem) {
            return;
        }

        $body = $this->findBodyByPlayerId($userId)->fetch();
        if (!$body) {
            return;
        }

        if ($bodySlot === 'face' && $inventoryItem->item->subtype === Item::ARMOR_SUBTYPE_HEADGEAR && $body->head) {
            return;
        }

        if ($inventoryItem->item->subtype === Item::WEAPON_SUBTYPE_TWO_HANDED_MELEE && $body->ranged && $body->ref('items', 'ranged')->subtype === Item::WEAPON_SUBTYPE_TWO_HANDED_RANGED) {
            $this->unequipItem($inventoryId, 'ranged', $this->findEmptySlot($userId), $userId);
        } else if ($inventoryItem->item->subtype === Item::WEAPON_SUBTYPE_TWO_HANDED_RANGED && $body->melee && $body->ref('items', 'melee')->subtype === Item::WEAPON_SUBTYPE_TWO_HANDED_MELEE) {
            $this->unequipItem($inventoryId, 'melee', $this->findEmptySlot($userId), $userId);
        }

        $inventoryItem->delete();

        if ($body->{$bodySlot}) {
            $this->unequipItem($inventoryId, $bodySlot, $slot, $userId);
        }

        $body->update([
            $bodySlot => $itemId,
        ]);

        $this->equipInventoryItem($itemId, $userId);

        $this->addGearStats($userId, $this->getGearStats($itemId));
    }

    public function getGearStats(int $itemId, bool $equip = true): array
    {
        $multiplier = $equip ? 1 : -1;
        $item = $this->database->table('items')->get($itemId);
        return [
            'strength' => ($item->strength ?? 0) * $multiplier,
            'stamina' => ($item->stamina ?? 0) * $multiplier,
            'speed' => ($item->speed ?? 0) * $multiplier,
            'attack' => ($item->attack ?? 0) * $multiplier,
            'armor' => ($item->armor ?? 0) * $multiplier,
            'energy_max' => ($item->energy_max ?? 0) * $multiplier,
            'xp_boost' => $multiplier === -1 ? 1 / ($item->xp_boost ?? 1) : ($item->xp_boost ?? 1),
        ];
    }

    public function unequipItem(int $inventoryId, string $bodySlot, int $slot, int $userId)
    {
        $equippedItem = $this->findEquippedItem($userId, $bodySlot);
        if (!$equippedItem) {
            return;
        }

        if (!$this->checkSlotIsEmpty($userId, $slot)) {
            return;
        }

        $this->findAllInventoryItems($inventoryId)->insert([
            'slot' => $slot,
            'item_id' => $equippedItem->id,
            'player_inventory_id' => $inventoryId,
            'quantity' => 1,
        ]);

        $this->addGearStats($userId, $this->getGearStats($equippedItem->id, false));

        $this->unequipInventoryItem($equippedItem->id, $userId);

        $this->findBodyByPlayerId($userId)->update([
            $bodySlot => null,
        ]);
    }

    public function moveItem(int $inventoryId, int $oldSlot, int $newSlot)
    {
        $inventoryItem = $this->findInventoryItem($inventoryId, $oldSlot)->fetch();
        if (!$inventoryItem) {
            return;
        }

        $otherItem = $this->findInventoryItem($inventoryId, $newSlot)->fetch();
        if ($otherItem) {
            $otherItem->update([
                'slot' => $oldSlot,
            ]);
        }

        $inventoryItem->update([
            'slot' => $newSlot,
        ]);
    }

    public function equipInventoryItem(int $itemId, int $userId)
    {
        $item = $this->itemsRepository->findAll()->get($itemId);
        if (!$item) {
            return false;
        }

        $inventory = $this->findByUser($userId);

        if (!$inventory) {
            $inventory = $this->createInventory($userId);
        }

        if ($item->type === Item::TYPE_MISC && $item->subtype === Item::MISC_SUBTYPE_INVENTORY) {
            $ability = json_decode($item->special_ability);
            $capacity = $ability->inventory;

            $inventory->update([
                'width' => $capacity->width,
                'height' => $capacity->height,
            ]);

            return true;
        }
    }

    public function unequipInventoryItem(int $itemId, int $userId)
    {
        $item = $this->itemsRepository->findAll()->get($itemId);
        if (!$item) {
            return false;
        }

        $inventory = $this->findByUser($userId);

        if ($item->type === Item::TYPE_MISC && $item->subtype === Item::MISC_SUBTYPE_INVENTORY) {
            $inventory->update([
                'width' => self::BASE_WIDTH,
                'height' => self::BASE_HEIGHT,
            ]);

            return true;
        }
    }
}
