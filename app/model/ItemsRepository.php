<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class ItemsRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('items');
    }
}
