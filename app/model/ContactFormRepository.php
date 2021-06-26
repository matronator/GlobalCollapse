<?php

namespace App\Model;

use Nette;


class ContactFormRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	public $uploadDir = '/upload/contact_form/';

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('contact_form');
	}
}
