<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class StatisticsRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function findAll(): Nette\Database\Table\Selection
    {
        return $this->database->table('statistics');
    }

    public function findByUser(int $userId): Nette\Database\Table\ActiveRow
    {
        $stats = $this->findAll()->where('user_id', $userId)->fetch();

        if (!$stats) {
            $stats = $this->findAll()->insert([
                'user_id' => $userId,
            ]);
        }

        return $stats;
    }
}
