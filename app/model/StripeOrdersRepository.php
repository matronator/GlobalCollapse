<?php

namespace App\Model;

use Nette;
use Stripe\StripeObject;
use Tracy\Debugger;

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
		Debugger::log($session, 'stripe-order');
		$eventId = $session->id;
		$check = $this->findAll()->insert([
			'stripe_id' => $eventId,
			'data' => $session->toJSON(),
			'status' => $type,
		]);
		Debugger::log($check, 'stripe-order');
	}

	public function saveOrder(StripeObject $session, string $event = 'checkout.session.completed')
	{
		$this->createOrder($session, $event);
	}
}
