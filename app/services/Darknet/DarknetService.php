<?php

declare(strict_types=1);

namespace App\Services\Darknet;

use App\Model\DrugsRepository;
use App\Model\UserRepository;
use Contributte\Translation\Translator;
use Nette\Database\Table\ActiveRow;
use Nette\Http\Session;
use Nette\Security\User;

class DarknetService
{
	public function __construct(public DrugsRepository $drugsRepository, public UserRepository $userRepository, public Session $session, public Translator $translator)
	{
	}

	public function offerSell(User $user, string $hash = null, int $quantity = null): DarknetResponse
	{
		$player = $this->userRepository->getUser($user->getIdentity()->id);

		$offer = $this->getOffer($hash, $quantity);
		if ($offer instanceof DarknetResponse) {
			return $offer;
		}

		$totalPrice = $this->drugsRepository->getOfferSellPrice($offer, $quantity);
		if ($offer->vendor->money < $totalPrice) {
			$newQuantity = $this->drugsRepository->getQuantityFromSellPrice($offer, $offer->vendor->money);
            $newPrice = $this->drugsRepository->getOfferSellPrice($offer, $newQuantity);
            return $this->sellDrugs($player, $offer, $newQuantity, $newPrice, true);
		}

        return $this->sellDrugs($player, $offer, $quantity, $totalPrice);
	}

	public function offerBuy(User $user, string $hash = null, int $quantity = null): DarknetResponse
	{
		$player = $this->userRepository->getUser($user->getIdentity()->id);

		$offer = $this->getOffer($hash, $quantity);
		if ($offer instanceof DarknetResponse) {
			return $offer;
		}

		$remainder = 0;
		if ($offer->quantity < $quantity) {
			$remainder = $quantity - $offer->quantity;
			$quantity = $offer->quantity;
		}

		$totalPrice = $this->drugsRepository->getOfferBuyPrice($offer, $quantity);
		if ($player->money < $totalPrice) {
			return new DarknetResponse($this->translator->translate('general.messages.danger.notEnoughMoney'), 'danger');
		}

		$this->drugsRepository->offerBuy($offer->id, $player->id, $quantity);
		$this->userRepository->addMoney($player->id, -$totalPrice);

		if ($remainder > 0) {
			return new DarknetResponse($this->translator->translate('general.messages.warning.purchaseWithRemainder', ['quantity' => $quantity]), 'warning');
		}
		return new DarknetResponse($this->translator->translate('general.messages.success.purchaseSuccessful'), 'success');
	}

	private function getOffer(?string $hash = null, ?int $quantity = null): ActiveRow|DarknetResponse
	{
		if ($hash == null || $quantity == null) {
			return new DarknetResponse($this->translator->translate('general.messages.danger.somethingFishy'), 'danger');
		}

		$sessionOffers = $this->session->getSection('darknetOffers');
		$oldOfferId = $sessionOffers[$hash];

		$offer = $this->drugsRepository->findOffer($oldOfferId)->fetch();
		if (!is_object($offer)) {
			return new DarknetResponse($this->translator->translate('general.messages.danger.somethingFishy'), 'danger');
		}

		if (!$offer->active) {
			return new DarknetResponse($this->translator->translate('general.messages.danger.somethingFishy'), 'danger');
		}

		return $offer;
	}

    private function sellDrugs(ActiveRow $player, ActiveRow $offer, int $quantity, int $totalPrice, bool $remainder = false): DarknetResponse
    {
        $playerDrug = $this->drugsRepository->findUserDrug($player->id, $offer->drug_id)->fetch();
        if ($playerDrug->quantity < $quantity) {
            return new DarknetResponse($this->translator->translate('general.messages.danger.orderSellTooMany'), 'danger');
        }

        $this->drugsRepository->offerSell($offer->id, $player, $quantity);
        $this->userRepository->addMoney($player->id, $totalPrice);

        if (!$remainder) {
            return new DarknetResponse($this->translator->translate('general.messages.success.drugsSold'), 'success');
        }

        return new DarknetResponse($this->translator->translate('general.messages.warning.drugsSoldWithRemainder', ['quantity' => $quantity]), 'warning');
    }
}
