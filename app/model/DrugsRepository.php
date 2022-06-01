<?php

namespace App\Model;

use Nette;

class DrugsRepository
{
	/** @var Nette\Database\Explorer */
	private $database;

	public function __construct(Nette\Database\Explorer $database)
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

	public function findVendorOffers(int $vendorId) {
		return $this->findAllOffers()->where('vendor_id', $vendorId);
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
			$newOfferQuantity = $offer->quantity - $quantity;
			$price = $this->getOfferBuyPrice($offer, $quantity);
			$this->findOffer($offerId)->update([
				'quantity-=' => $quantity,
				'buys+=' => 1
			]);
			$this->findVendor(0, $offerId)->update([
				'money+=' => $price,
				'sells+=' => 1
			]);
			$this->buyDrugs($userId, $offer->drug_id, $quantity);
			if ($newOfferQuantity <= 0) {
				$this->changeOffer($offerId);
			}
		}
	}

	public function offerSell(int $offerId, $user, int $quantity)
	{
		$offer = $this->findOffer($offerId)->fetch();
		if ($offer->vendor->level >= (int) max(min(round($user->player_stats->level / 10), 10), 1)) {
			$price = $this->getOfferSellPrice($offer, $quantity);
			$this->findOffer($offerId)->update([
				'quantity+=' => $quantity,
				'sells+=' => 1
			]);
			$this->findVendor(0, $offerId)->update([
				'money-=' => $price,
				'buys+=' => 1
			]);

			$this->sellDrug($user->id, $offer->drug_id, $quantity);

			$vendor = $this->findVendor($offer->vendor_id)->fetch();
			if ($vendor->money <= $offer->drug->price * 2) {
				$this->updateOfferAndMoney($offerId);
			}
			// $this->changeOffer($offerId);
		}
	}

	private function changeOffer(int $offerId)
	{
		$offer = $this->findOffer($offerId)->fetch();
		if ($offer->quantity <= 0) {
			// $baseMoney = $offer->vendor->base_money;
			$drug = $offer->drug_id;
			$drugArray = [];
			for ($i = 1; $i <= 5; $i++) {
				if ($i != $drug) {
					array_push($drugArray, $i);
				}
			}
			shuffle($drugArray);
			// $this->findVendor($offer->vendor_id)->update([
			// 	'base_money' => $baseMoney
			// ]);
			$newQuantity = rand(500, 2000) * pow($offer->vendor->level, 1.05);
			$this->findOffer($offerId)->update([
				'drug_id' => array_pop($drugArray),
				'quantity' => $newQuantity
			]);
		}
	}

	public function updateOfferAndMoney(int $offerId)
	{
		$offer = $this->findOffer($offerId)->fetch();

		$vendor = $this->findVendor($offer->vendor_id);
		if ($vendor->fetch()->money <= $offer->drug->price * 2) {
			$baseMoney = $offer->vendor->base_money;
			$vendor->update([
				'money' => $baseMoney
			]);
		}
		$drug = $offer->drug_id;
		$drugArray = [];
		for ($i = 1; $i <= 5; $i++) {
			if ($i != $drug) {
				array_push($drugArray, $i);
			}
		}
		shuffle($drugArray);
		$newQuantity = rand(500, 2000) * pow($offer->vendor->level, 1.05);
		$this->findOffer($offerId)->update([
			'drug_id' => array_pop($drugArray),
			'quantity' => $newQuantity
		]);
	}

	public function getOfferBuyPrice($offer, int $quantity = 0): int
	{
		$vendorFee = 1 + $offer->vendor->charge;
		return (int) round(($quantity * $offer->drug->price) * $vendorFee, 0);
	}

	public function getOfferSellPrice($offer, int $quantity = 0): int
	{
		$vendorFee = 1;
		return (int) round(($quantity * $offer->drug->price) * $vendorFee, 0);
	}
}
