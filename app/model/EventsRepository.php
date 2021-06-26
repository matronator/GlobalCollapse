<?php

namespace App\Model;

use Nette;
use Nette\Utils\ArrayHash;

class EventsRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('game_events');
	}

	public function findEventById(?int $eventId = null)
	{
		return $this->database->table('game_events')->where('id', $eventId);
	}

	public function findEventBySlug(?int $eventSlug = null)
	{
		return $this->database->table('game_events')->where('slug', $eventSlug);
	}

	public function checkEvent(?int $eventSlug = null)
	{
		if ($this->findEventBySlug($eventSlug)->fetch()->active == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function getActive()
	{
		return $this->database->table('game_events')->where('active', 1);
	}
}
