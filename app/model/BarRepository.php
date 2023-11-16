<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class BarRepository
{
    /** @var Nette\Database\Explorer */
    private Nette\Database\Explorer $database;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function getJobDuration(int $duration, int $playerLevel, int $tier): int
    {
        $result = (int) round(min(round(($duration * $playerLevel) / 15, 2), $duration));
        return (int) (UserRepository::getPremiumDuration($result, $tier) * 60);
    }
}
