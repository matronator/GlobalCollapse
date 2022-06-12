<?php

namespace App\Model;

use Nette;

class MiscRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function findAllExternalVisits()
	{
		return $this->database->table('external_visits');
	}
}
