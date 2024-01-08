<?php

namespace App\Model;

use Nette;
use Stripe\StripeObject;

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

	public function createOrder(StripeObject $session, string $type = 'awaiting orders')
	{
		$eventId = $session->id;
		return $this->findAll()->insert([
			'stripe_id' => $eventId,
			'data' => $session->toJSON(),
			'status' => $type,
		]);
	}

	public function saveOrder(StripeObject $session, string $event = 'checkout.session.completed')
	{
		$this->createOrder($session, $event);
	}
}
