<?php

namespace App\Model;

use Nette;
use Nette\Utils\ArrayHash;

class BuildingsRepository
{
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('buildings');
	}

	public function findById(?int $bId = null)
	{
		return $this->database->table('buildings')->where('id', $bId);
  }

	public function findPlayerBuildings(?int $userId = null)
	{
		return $this->database->table('player_buildings')->where('user_id', $userId);
	}

	public function findPlayerLand(?int $userId = null)
	{
		return $this->database->table('player_buildings')->where('user_id = ? && buildings_id = ?', $userId, 1);
	}
}
