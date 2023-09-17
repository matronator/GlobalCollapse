<?php

namespace App\Model;

use Nette;


class StripeOrdersRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('stripe_orders');
	}

	public function createOrder($session, string $type = 'awaiting orders')
	{
		$this->findAll()->insert([
			'data' => $session,
			'status' => $type,
		]);
	}

	public function fulfillOrder($session)
	{
		$this->createOrder($session, 'fulfilled');
	}
}
