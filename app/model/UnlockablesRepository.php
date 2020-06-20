<?php

namespace App\Model;

use Nette;

class UnlockablesRepository
{
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('unlockables');
	}

	public function findPlayerUnlocked(int $userId)
	{
		return $this->database->table('player_unlocked')->where('id', $userId);
	}
}
