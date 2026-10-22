<?php

namespace App\Model;

use DateTime;
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

	public function pageHits()
	{
		$row = $this->database->table('page_hits')->fetch();
		if (!$row) {
			$row = $this->database->table('page_hits')->insert([
				'last_update' => new DateTime(),
				'hits_this_month' => 0,
				'hits_total' => 0,
			]);
		}

		return $row;
	}

	public function recordHit()
	{
		$row = $this->pageHits();
		if ($row->last_update->format('Y-m') !== (new DateTime())->format('Y-m')) {
			$row->update([
				'hits_this_month' => 0,
				'last_update' => new DateTime(),
			]);
		}
		$row->update([
			'hits_this_month+=' => 1,
			'hits_total+=' => 1,
			'last_update' => new DateTime(),
		]);
	}
}
