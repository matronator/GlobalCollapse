<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class InventoryRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    public const BASE_WIDTH = 4;
    public const BASE_HEIGHT = 3;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
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
            return;
        }

        return $this->database->table('items')->get($body->{$bodySlot});
    }

    public function createBody(int $userId)
    {
        if ($this->findBodyByPlayerId($userId)) {
            return;
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
        ]);
    }

    public function equipItem(int $inventoryId, int $itemId, string $bodySlot, int $slot, int $userId)
    {
        $inventoryItem = $this->findInventoryItem($inventoryId, $slot);
        if (!$inventoryItem) {
            return;
        }

        $body = $this->findBodyByPlayerId($userId)->fetch();
        if (!$body) {
            return;
        }

        $inventoryItem->delete();

        if ($body->{$bodySlot}) {
            $this->unequipItem($inventoryId, $bodySlot, $slot, $userId);
        }

        $body->update([
            $bodySlot => $itemId,
        ]);
    }

    public function unequipItem(int $inventoryId, string $bodySlot, int $slot, int $userId)
    {
        $equippedItem = $this->findEquippedItem($userId, $bodySlot);
        if (!$equippedItem) {
            return;
        }

        $this->findAllInventoryItems($inventoryId)->insert([
            'slot' => $slot,
            'item_id' => $equippedItem->id,
            'player_inventory_id' => $inventoryId,
            'quantity' => 1,
        ]);

        $this->findBodyByPlayerId($userId)->update([
            $bodySlot => null,
        ]);
    }

    public function moveItem(int $inventoryId, int $oldSlot, int $newSlot)
    {
        $inventoryItem = $this->findInventoryItem($inventoryId, $oldSlot);
        if (!$inventoryItem) {
            return;
        }

        $inventoryItem->update([
            'slot' => $newSlot,
        ]);
    }
}
