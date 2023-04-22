<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class ItemsRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    public const IMAGES_UPLOAD_DIR = '/data/items/';
    public const IMAGES_DIR = '/dist/front/images/items/';

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('items');
    }

    public function get(int $id): Nette\Database\Table\ActiveRow
    {
        return $this->findAll()->get($id);
    }
}
