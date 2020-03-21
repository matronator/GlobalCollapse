<?php

namespace App\Model;

use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Database\Context;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;

const USER_ROLE_ADMIN = 'a';
const USER_ROLE_USER = 'u';

class UserRepository
{
    /** @var Nette\Database\Context */
    private $database;

    private $expGain = 1;
    private $expMaxBase = 150;
    private $maxEnergyBase = 100;

    public $roles = [
        USER_ROLE_ADMIN => 'Admin',
        USER_ROLE_USER => 'User',
    ];

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('user');
    }

    public function getUser(?int $id = null): ?ActiveRow
    {
        if (!$id)
            return null;
        return $this->findAll()
            ->wherePrimary($id)
            ->fetch();
    }

    public function deleteUser(?int $id = null): ?ActiveRow
    {
        $user = $this->getUser($id);
        if (!$user)
            throw new BadRequestException('Uživatel nexistuje');
        $this->findAll()->wherePrimary($id)->delete();
        return $user;
    }

    public function createUser(ArrayHash $values): object
    {
        $userMail = $this->findAll()->where('email', $values->email)->fetch();
        $userName = $this->findAll()->where('username', $values->username)->fetch();
        if ($userMail)
            throw new BadRequestException('Account with this email address already exists.');
        if ($userName)
            throw new BadRequestException('Username taken.');
        $values->registration = new \DateTime();
        $values->date_log = new \DateTime();
        $values->ip = $_SERVER["REMOTE_ADDR"];
        $user = $this->findAll()->insert($values);
        // $this->mailService->sendPasswordLink($user);
        return $user;
    }

    public function updateUser(int $id, ArrayHash $values): ActiveRow
    {
        $this->findAll()->wherePrimary($id)->update($values);
        return $this->getUser($id);
    }

    /**
     * Add experience points to player
     *
     * @param integer $id
     * @param integer $xp
     * @return void
     */
    public function addXp(int $id, $xp) {
        $player = $this->getUser($id);
        $xpNow = $player->xp;
        $xpMax = $player->xp_max;
        $level = $player->level;
        $newXp = $xpNow + $xp;
        $this->getUser($id)->update([
            'xp' => $newXp
        ]);
        while ($xpMax <= $newXp) {
            $level += 1;
            $xpMax = $this->levelUp($id, $level);
        }
    }

    /**
     * Level up the player
     *
     * @param integer $id
     * @param integer $lvl
     * @return void
     */
    public function levelUp(int $id, int $lvl) {
        $newLevel = $lvl + 1;
        $energy = $this->getUser($id)->energy_max;
        if ($newLevel <= 10) {
            $energy = $this->getMaxEnergy($newLevel);
        }
        $oldMax = $this->getUser($id)->xp_max;
        $newMax = $this->getMaxExp($newLevel);
        $this->getUser($id)->update([
            'xp_min' => $oldMax,
            'xp_max' => $newMax,
            'level' => $newLevel,
            'energy' => $energy,
            'energy_max' => $energy
        ]);
        return $newMax;
    }

    /**
     * Function to calculate max_xp for each level
     * Equation:
     * (round(level^baseGain)*(level^(level/baseXp))) * baseXp
     *
     * @param integer $lvl
     * @return int
     */
    private function getMaxExp(int $lvl): int {
        return round(pow($lvl, $this->expGain) * pow($lvl, ($lvl / $this->expMaxBase))) * $this->expMaxBase;
    }

    /**
     * Function to calculate energy_max for each level up to 10
     * Levels 10 and up have 200 energy
     * Equation:
     * level^2 + maxEnergyBase
     *
     * @param integer $lvl
     * @return int
     */
    private function getMaxEnergy(int $lvl): int {
        return pow($lvl, 2) + $this->maxEnergyBase;
    }
}
