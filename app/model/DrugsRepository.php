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

	// ---------------------------------------
	// -------- DRUGS & PLAYER STASH ---------
	// ---------------------------------------

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

	public function sellDrug(?int $userId = null, ?int $drugId = null, ?int $qtty = 0)
	{
		$drugInv = $this->findUserDrug($userId, $drugId)->fetch();
		if ($drugInv && $drugInv->quantity >= $qtty) {
			if ($qtty > 0) {
				$this->findInventory()->where('user_id = ? && drugs_id = ?', $userId, $drugId)->update([
					'quantity-=' => $qtty
				]);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function buyDrugs(?int $userId = null, ?int $drugId = null, ?int $qtty = 0)
	{
		$drugInv = $this->findUserDrug($userId, $drugId)->fetch();
		if ($drugInv) {
			if ($qtty > 0) {
				$this->findInventory()->where('user_id = ? && drugs_id = ?', $userId, $drugId)->update([
					'quantity+=' => $qtty
				]);
				return true;
			} else {
				return false;
			}
		} else {
			if ($qtty > 0) {
				$this->findInventory()->where('user_id = ? && drugs_id = ?', $userId, $drugId)->insert([
					'user_id' => $userId,
					'drugs_id' => $drugId,
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

	// ---------------------------------------
	// ----------- DARKNET VENDORS -----------
	// ---------------------------------------

	public function findAllVendors()
	{
		return $this->database->table('vendors');
	}

	public function findVendor(int $vendorId, ?int $offerId = null)
	{
		if ($offerId == null) {
			return $this->database->table('vendors')->where('id', $vendorId);
		} else {
			$offer = $this->findOffer($offerId)->fetch();
			return $this->database->table('vendors')->where('id', $offer->vendor_id);
		}
	}

	public function findAllOffers()
	{
		return $this->database->table('vendor_offers');
	}

	public function findAvailableOffers(int $playerLevel)
	{
		$vendorLevel = (int) max(min(round($playerLevel / 10), 10), 1);
		return $this->database->table('vendor_offers')->where('vendor.level', $vendorLevel);
	}

	public function findOffer(int $offerId)
	{
		return $this->database->table('vendor_offers')->where('id', $offerId);
	}

	public function createOffer(int $vendorId, int $drugId, int $quantity, int $limit, ?int $active = 1)
	{
		$this->findAllOffers()->insert([
			'vendor_id' => $vendorId,
			'drug_id' => $drugId,
			'quantity' => $quantity,
			'limit' => $limit,
			'active' => $active
		]);
	}

	public function offerBuy(int $offerId, int $userId, int $quantity)
	{
		$offer = $this->findOffer($offerId)->fetch();
		if ($offer->quantity >= $quantity) {
			$price = (int) round(($quantity * $offer->drug->price) * 1.05, 0);
			$this->findOffer($offerId)->update([
				'quantity-=' => $quantity
			]);
			$this->findVendor(0, $offerId)->update([
				'money+=' => $price
			]);
			$this->buyDrugs($userId, $offer->drug_id, $quantity);
		}
	}
}
