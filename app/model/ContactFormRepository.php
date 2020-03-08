<?php

namespace App\Model;

use Nette;


class ContactFormRepository
{
	/** @var Nette\Database\Context */
	private $database;

	public $uploadDir = '/upload/contact_form/';

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('contact_form');
	}
}
