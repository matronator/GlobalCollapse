<?php

namespace App\Model;

use Nette;
use Nette\Utils\ArrayHash;

class DrugsRepository
{
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('drugs');
	}

	public function findDrugInventory(?int $userId = null)
	{
		return $this->database->table('drugs_inventory')->where('user_id', $userId);
	}

	public function findUserDrug(?int $userId = null, ?int $drugId = null)
	{
		return $this->database->table('drugs_inventory')->where('user_id = ? && drug_id = ?', $userId, $drugId);
	}

	public function updateUserDrug(?int $userId = null, ?int $drugId = null, ?int $qtty = 0)
	{
		$drugInv = $this->findUserDrug($userId, $drugId)->fetch();
		if ($drugInv) {
			$drugInv->amount = intval($drugInv->amount) + $qtty;
			$this->findUserDrug($userId, $drugId)->update($drugInv);
			return $this->findUserDrug($userId, $drugId);
		} else {
			$obj = new ArrayHash;
			$obj->user_id = $userId;
			$obj->drug_id = $drugId;
			$obj->amount = $qtty;
			$this->findUserDrug($userId, $drugId)->insert($obj);
			return $obj;
		}
	}

	public function findDrug(?int $drugId = null)
	{
		return $this->database->table('drugs')->where('id', $drugId);
	}
}
