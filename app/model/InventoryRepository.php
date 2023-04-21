<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class InventoryRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

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
}
