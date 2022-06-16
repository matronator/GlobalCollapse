<?php

namespace App\Model;

use DateTime;
use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;
use Nette\Security\Passwords;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;

const USER_ROLE_ADMIN = 'a';
const USER_ROLE_USER = 'u';

class UserRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    private $expGain = 1;
    private $expMaxBase = 50;
    private $maxEnergyBase = 100;

    public $maxPlayerMoney = 999_999_999;

    public $roles = [
        USER_ROLE_ADMIN => 'Admin',
        USER_ROLE_USER => 'User',
    ];

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('user');
    }

    public function getTotalPlayers()
    {
        return count($this->database->table('user'));
    }

    public function getOnlinePlayers()
    {
        $now = new DateTime('now');
        $plusDelay = $now->getTimestamp();
        $plusDelay -= 900;
        $now->setTimestamp($plusDelay);
        $activityTime = $now->format('Y-m-d H:i:s');
        return count($this->database->table('user')->where('last_active >= ?', $activityTime));
    }

    public function findUsers(?string $sortBy = 'power')
    {
        return $this->database->table('user')->order('player_stats.' . $sortBy . ' DESC');
    }

    public function findAllStats()
    {
        return $this->database->table('player_stats');
    }

    public function findAllVoteRewards()
    {
        return $this->database->table('vote_rewards');
    }

    public function canVoteAgain(int $userId)
    {
        $vote = $this->findAllVoteRewards()->where('user_id', $userId)->fetch();
        if ($vote) {
            $votedDate = strtotime($vote->voted_at);
            $weekAgo = strtotime('-1 week');
            if ($weekAgo <= $votedDate) {
                $timeLeft = $votedDate - $weekAgo;
                $daysLeft = floor($timeLeft / 86400); // 86400 = seconds per day
                $hoursLeft = floor(($timeLeft - $daysLeft * 86400) / 3600); // 3600 = seconds per hour
                return (object) [
                    'days' => $daysLeft,
                    'hours' => $hoursLeft,
                ];
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function getSettings(int $userId)
    {
        $settings = $this->database->table('user_settings')->where('user_id', $userId);
        if ($settings->count() <= 0) {
            $settings->insert([
                'user_id' => $userId
            ]);
        }
        return $settings;
    }

    public function getUserTimezone(int $userId)
    {
        return $this->getSettings($userId)->fetch();
    }

    public function findAllActions()
    {
        return $this->database->table('actions');
    }

    public function getUser(?int $id = null): ?ActiveRow
    {
        if (!$id)
            return null;
        return $this->findAll()
            ->wherePrimary($id)
            ->fetch();
    }

    public function getUserByName(?string $username = null): ?ActiveRow
    {
        if (!$username)
            return null;
        return $this->findAll()
            ->where('username', $username)
            ->fetch();
    }

    public function getUserByEmail(?string $email = null): ?ActiveRow
    {
        if (!$email)
            return null;
        return $this->findAll()
            ->where('email', $email)->fetch();
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
        $stats = $this->createStats();
        $actions = $this->createActions();
        $values->player_stats_id = $stats->id;
        $values->actions_id = $actions->id;
        $user = $this->findAll()->insert($values);
        // $this->mailService->sendPasswordLink($user);
        return $user;
    }

    public function changeUserPassword(int $userId, string $password) {
        return $this->updateUser($userId, [
            'password' => $password
        ]);
    }

    public function changeUserEmail(int $userId, string $email) {
        return $this->updateUser($userId, [
            'email' => $email
        ]);
    }

    public function findAllUserPasswordReset(): Selection
    {
        return $this->database->table('user_password_reset');
    }

    public function generateResetPasswordHash(int $userId): string
    {
        $uuid = Uuid::uuid4();
        $guid = $uuid->toString();
        $recoveries = $this->findAllUserPasswordReset()->where('user_id', $userId)->where('reset', 0);
        if ($recoveries->count() > 0) {
            return $recoveries->fetch()->hash;
        }
        $this->findAllUserPasswordReset()->insert([
            'user_id' => $userId,
            'hash' => $guid,
        ]);
        return $guid;
    }

    public function resetPassword(int $userId)
    {
        $this->findAllUserPasswordReset()->where('user_id', $userId)->update([
            'date_reset' => new DateTime,
            'reset' => 1,
        ]);
    }

    public function getUserByRecoveryHash(string $hash): ?ActiveRow
    {
        $passwordReset = $this->findAllUserPasswordReset()->where('hash', $hash)->where('reset', 0)->fetch();
        return $this->getUser($passwordReset->user_id);
    }

    public function cypherPassword(string $password): string
    {
        $passwords = new Passwords(PASSWORD_BCRYPT, ['cost' => 10]);
        $hash = $passwords->hash($password);
        return $hash;
    }

    public function createStats()
    {
        return $this->findAllStats()->insert([]);
    }

    public function updateStats(int $userId, ?int $strength = -99, ?int $stamina = -99, ?int $speed = -99)
    {
        $oldStats = $this->getUser($userId);
        $newStrength = $strength == -99 ? $oldStats->player_stats->strength : $strength;
        $newStamina = $stamina == -99 ? $oldStats->player_stats->stamina : $stamina;
        $newSpeed = $speed == -99 ? $oldStats->player_stats->speed : $speed;
        $this->getUser($userId)->ref('player_stats', 'player_stats_id')->update([
            'strength' => $newStrength,
            'stamina' => $newStamina,
            'speed' => $newSpeed,
            'power' => $strength + $stamina + $speed
        ]);
    }

    public function updateStatsAdd(int $userId, ?int $strength = 0, ?int $stamina = 0, ?int $speed = 0)
    {
        $oldStats = $this->getUser($userId);
        $oldStrength = $oldStats->player_stats->strength;
        $oldStamina = $oldStats->player_stats->stamina;
        $oldSpeed = $oldStats->player_stats->speed;
        $oldPower = $oldStrength + $oldStamina + $oldSpeed;

        $newPower = $oldPower + $strength + $stamina + $speed;
        $this->getUser($userId)->ref('player_stats', 'player_stats_id')->update([
            'strength+=' => $strength,
            'stamina+=' => $stamina,
            'speed+=' => $speed,
            'power' => $newPower
        ]);
    }

    public function createActions()
    {
        return $this->findAllActions()->insert([]);
    }

    public function updateUser(int $id, iterable $values): ActiveRow
    {
        $this->findAll()->wherePrimary($id)->update($values);
        return $this->getUser($id);
    }

    public function addMoney(int $id, $amount)
    {
        if (is_numeric($amount)) {
            $userMoney = $this->getUser($id)->money;
            $total = (int)max(round($userMoney + $amount, 0), 0);
            $newTotal = $total <= $this->maxPlayerMoney ? $total : $this->maxPlayerMoney;
            $this->findAll()->wherePrimary($id)->update([
                'money' => $newTotal
            ]);
        }
    }

    public function addEnergy(int $id, $energy) {
        $this->getUser($id)->player_stats->update([
            'energy+=' => $energy
        ]);
    }

    public function increaseMaxEnergy(int $userId, int $newEnergy) {
        $user = $this->getUser($userId);
        if ($user->player_stats->energy_max < $newEnergy) {
            $user->player_stats->update([
                'energy_max' => $newEnergy
            ]);
        }
    }

    /** Job rewards */
    public function getRewardMoney($jobmoney, $level) {
		return $jobmoney + (int)round($jobmoney * ($level - 1) * 0.08);
	}

	public function getRewardXp($jobxp, $level) {
		return $jobxp + round($jobxp * ($level - 1) * 0.1);
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
        $xpNow = $player->player_stats->xp;
        $xpMax = $player->player_stats->xp_max;
        $level = $player->player_stats->level;
        $this->getUser($id)->player_stats->update([
            'xp+=' => $xp
        ]);
        $newXp = $xpNow + $xp;
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
        $player = $this->getUser($id);
        $newLevel = $lvl + 1;
        $energy = $player->player_stats->energy_max;
        if ($newLevel <= 10) {
            $energy = $this->getMaxEnergy($newLevel);
        }
        $oldMax = $player->player_stats->xp_max;
        $newMax = $this->getMaxExp($newLevel);
        $sp = $player->skillpoints + 1;
        $this->getUser($id)->update([
            'skillpoints' => $sp
        ]);
        $this->getUser($id)->player_stats->update([
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
     * OLD FORMULA
     * (round(level^baseGain)*(level^(level/baseXp))) * baseXp
     *
     * NEW FORMULA
     * round(level^2 * log10(level), -2) + baseGain*level
     *
     * @param integer $lvl
     * @return int
     */
    private function getMaxExp(int $lvl): int {
        return round((pow($lvl, 2) * log($lvl)), -1) + ($this->expMaxBase * $lvl);
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
