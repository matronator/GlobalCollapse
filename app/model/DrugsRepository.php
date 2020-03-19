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

	public function findInventory()
	{
		return $this->database->table('drugs_inventory');
	}

	public function findUserDrug(?int $userId = null, ?int $drugId = null)
	{
		return $this->database->table('drugs_inventory')->where('user_id = ? && drugs_id = ?', $userId, $drugId);
	}

	public function updateUserDrug(?int $userId = null, ?int $drugId = null, ?int $qtty = 0)
	{
		$drugInv = $this->findUserDrug($userId, $drugId)->fetch();
		if ($drugInv) {
			$amount = intval($drugInv->quantity) + $qtty;
			if ($amount >= 0) {
				$this->findInventory()->where('user_id = ? && drugs_id = ?', $userId, $drugId)->update([
					'quantity' => $amount
				]);
				return true;
			} else {
				return false;
			}
		} else {
			if ($qtty > 0) {
				$this->findInventory()->where('user_id = ? && drugs_id = ?', $userId, $drugId)->insert([
					'quantity' => $qtty
				]);
				return true;
			} else {
				return false;
			}
		}
	}

	public function findDrug(?int $drugId = null)
	{
		return $this->database->table('drugs')->where('id', $drugId);
	}
}
