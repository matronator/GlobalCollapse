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

    public function findAllItems(int $id)
    {
        return $this->database->table('inventory_item')->where('player_inventory_id', $id);
    }
}
