<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\UserRepository;
use App\Model\DrugsRepository;
use DateTime;
use Nette\Application\UI\Form;
use ActionLocker;
use Nette\Application\UI\Multiplier;
use Timezones;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class CityPresenter extends GamePresenter
{

	public $userRepository;
	private $drugsRepository;

	public function __construct(
		UserRepository $userRepository,
		DrugsRepository $drugsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->drugsRepository = $drugsRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDarknet() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$drugs = $this->drugsRepository->findAll()->fetchAll();
		$this->template->drugs = $drugs;
		$drugsInventory = $this->drugsRepository->findDrugInventory($player->id)->order('drugs_id', 'ASC')->fetchAll();
		$playerDrugs = [];
		if (count($drugsInventory) > 0) {
			foreach ($drugsInventory as $drug) {
				$playerDrugs[$drug->drugs->name] = $drug->quantity;
			}
			// $this->template->drugsInventory = $drugsInventory;
		} else {
			foreach ($drugs as $drug) {
				$playerDrugs[$drug->name] = 0;
			}
		}
		$this->template->playerDrugs = $playerDrugs;
		// Vendors
		$vendors = $this->drugsRepository->findAllVendors()->where('active', 1);
		$this->template->vendors = $vendors;
		$offers = $this->drugsRepository->findAvailableOffers($player->player_stats->level)->where('vendor_offers.active', 1)->order('vendor_offers.drug_id');
		$vendorOffers = [];
		$sessionOffers = $this->session->getSection('darknetOffers');
		foreach($offers as $offer) {
			$timestamp = (string)time();
			$bytes = random_bytes(5);
			$hash =  $timestamp . $offer->id . bin2hex($bytes);
			$vendorOffers[$offer->id] = $hash;
			$vendorOffers[$hash] = $offer;
			$sessionOffers[(string) $offer->id] = $hash;
			$sessionOffers[$hash] = $offer->id;
		}
		$this->template->vendorOffers = $vendorOffers;
		$this->template->offers = $offers;
		if ($player->actions->offer_refreshed) {
			$offerRefreshed = self::checkDates($player->actions->offer_refreshed, new DateTime());
			if ($offerRefreshed > 0) {
				$this->template->offerRefresh = $offerRefreshed;
			} else {
				$this->template->offerRefresh = false;
			}
		} else {
			$this->template->offerRefresh = false;
		}
	}

	public function actionRefreshOffer(string $hash)
	{
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);

		if ($player->actions->offer_refreshed) {
			$diff = self::checkDates($player->actions->offer_refreshed, new DateTime());
			if ($diff > 0) {
				$this->flashMessage($this->translator->translate('general.messages.warning.cantRefreshOffer', ['minutes' => $diff]), 'warning');
				$this->redirect('City:darknet');
			}
		}
		$sessionOffers = $this->session->getSection('darknetOffers');
		$oldOfferId = $sessionOffers[$hash];
		$offer = $this->drugsRepository->findOffer($oldOfferId)->fetch();
		if (!$offer || !$offer->active) {
			$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
			$this->redirect('City:darknet');
		}

		$allDrugs = $this->drugsRepository->findAll()->fetchAll();
		$drugs = [];
		foreach ($allDrugs as $drug) {
			if ($drug->id !== $offer->drug_id) {
				$drugs[] = $drug->id;
			}
		}
		shuffle($drugs);
		$this->drugsRepository->findVendor($offer->vendor_id)->update([
			'base_money' => $offer->vendor->base_money
		]);
		$this->drugsRepository->findOffer($offer->id)->update([
			'drug_id' => array_pop($drugs),
			'quantity' => rand(500, 2000) * pow($offer->vendor->level, 1.05),
			'buys' => 0,
			'sells' => 0
		]);
		$this->userRepository->getUser($player->id)->actions->update([
			'offer_refreshed' => new DateTime()
		]);

		$this->flashMessage($this->translator->translate('general.messages.success.offerRefreshed'), 'success');
		$this->redirect('City:darknet');
	}

	protected function createComponentVendorOfferForm(): Multiplier
	{
		$multi = new Multiplier(function ($offerId) {
			$form = new Form;
			$form->addInteger('offerInput')
				->setHtmlAttribute('class', 'uk-input darknet-offer-input')
				->setHtmlId('offerInput' . $offerId)
				->setHtmlAttribute('placeholder', 'Enter amount')
				->setRequired();

			$form->addSubmit('offerBuy', 'Buy')
				->setHtmlId('offerBuy-' . $offerId)
				->setHtmlAttribute('class', 'uk-button uk-button-small uk-button-primary')
				->setHtmlAttribute('data-offer-button', 'buy');

			$form->addSubmit('offerSell', 'Sell')
				->setHtmlId('offerSell-' . $offerId)
				->setHtmlAttribute('class', 'uk-button uk-button-small uk-button-danger')
				->setHtmlAttribute('data-offer-button', 'sell');

			$form->addHidden('offerId', $offerId);

			$form->onSuccess[] = [$this, 'processOfferForm'];

			return $form;
		});
		return $multi;
	}

	public function processOfferForm(Form $form, $values)
	{
		$control = $form->isSubmitted();
		if ($control->name == 'offerBuy') {
			$quantity = $values['offerInput'];
			$hash = $values['offerId'];
			$this->offerBuy($hash, $quantity);
		} else if ($control->name == 'offerSell') {
			$quantity = $values['offerInput'];
			$hash = $values['offerId'];
			$this->offerSell($hash, $quantity);
		}
	}

	public function renderWastelands() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$actionLocker = new ActionLocker();
		$actionLocker->checkActions($player, $this);
		$session = $this->session;
		$section = $session->getSection('returnedScavenging');
		if (isset($section['returnedScavenging'])) {
			$this->template->returned = true;
			$this->template->hours = $section['hours'];
			$this->template->money = $section['money'];
			$this->template->xpoints = $section['exp'];
			unset($section->returnedScavenging);
		}
		$isScavenging = $player->actions->scavenging;
		$this->template->scavenging = $isScavenging;
		if ($isScavenging > 0) {
			$scavengingSince = $player->actions->scavenge_start;
			$this->template->scavengingSince = Timezones::getUserTime($scavengingSince, $this->userPrefs->timezone);
			$nowDate = new DateTime();
			$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp());
			if ($diff < 3600) {
				$this->template->timePassed = round($diff / 60) . ' minutes';
			} else if ($diff <= 5400) {
				$this->template->timePassed = round($diff / 3600) . ' hour';
			} else {
				$this->template->timePassed = round($diff / 3600) . ' hours';
			}
		}
	}

	private function offerBuy(string $hash = null, int $quantity = null)
	{
		if ($hash == null || $quantity == null) {
			$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
			$this->redirect('City:darknet');
		} else {
			$sessionOffers = $this->session->getSection('darknetOffers');
			$oldOfferId = $sessionOffers[$hash];
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$offer = $this->drugsRepository->findOffer($oldOfferId)->fetch();
			if (is_object($offer)) {
				if ($offer->active) {
					if ($offer->quantity >= $quantity) {
						$totalPrice = $this->drugsRepository->getOfferBuyPrice($offer, $quantity);
						if ($player->money >= $totalPrice) {
							$this->drugsRepository->offerBuy($offer->id, $player->id, $quantity);
							$this->userRepository->addMoney($player->id, -$totalPrice);
							$this->flashMessage($this->translate('general.messages.success.purchaseSuccessful'), 'success');
						} else {
							$this->flashMessage($this->translate('general.messages.danger.notEnoughMoney'), 'danger');
						}
					} else {
						$this->flashMessage($this->translate('general.messages.danger.orderBuyTooMany'), 'danger');
					}
				} else {
					$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
				}
			} else {
				$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
			}
		}
	}

	private function offerSell(string $hash = null, int $quantity = null)
	{
		if ($hash == null || $quantity == null) {
			$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
			$this->redirect('City:darknet');
		} else {
			$sessionOffers = $this->session->getSection('darknetOffers');
			$oldOfferId = $sessionOffers[$hash];
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$offer = $this->drugsRepository->findOffer($oldOfferId)->fetch();
			if (is_object($offer)) {
				if ($offer->active) {
					$totalPrice = $this->drugsRepository->getOfferSellPrice($offer, $quantity);
					if ($offer->vendor->money >= $totalPrice) {
						$playerDrug = $this->drugsRepository->findUserDrug($player->id, $offer->drug_id)->fetch();
						if ($playerDrug->quantity >= $quantity) {
							$this->drugsRepository->offerSell($offer->id, $player, $quantity);
							$this->userRepository->addMoney($player->id, $totalPrice);
							$this->flashMessage($this->translate('general.messages.success.drugsSold'), 'success');
						} else {
							$this->flashMessage($this->translate('general.messages.danger.orderSellTooMany'), 'danger');
						}
					} else {
						$this->flashMessage($this->translate('general.messages.danger.orderSellVendorLow'), 'danger');
					}
				} else {
					$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
				}
			} else {
				$this->flashMessage($this->translate('general.messages.danger.somethingFishy'), 'danger');
			}
		}
	}

	public function createComponentScavengeForm(): Form {
		$form = new Form();
		$form->addSubmit('scavenge', 'Go scavenging');
		$form->addSubmit('stopScavenging', 'Return from scavenging');
		$form->onSuccess[] = [$this, 'scavengeFormSucceeded'];
		return $form;
	}

	public function scavengeFormSucceeded(Form $form, $values): void {
		$control = $form->isSubmitted();
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$isScavenging = $player->actions->scavenging;
		$isOnMission = $player->actions->on_mission;
		if ($control->name == 'scavenge') {
			if ($isScavenging <= 0 && $isOnMission <= 0) {
				$playerScavengeStart = new DateTime();
				$this->userRepository->getUser($player->id)->actions->update([
					'scavenging' => 1,
					'scavenge_start' => $playerScavengeStart
				]);
				$this->flashMessage($this->translate('general.messages.success.scavengingStart'), 'success');
			}
		} else if ($control->name == 'stopScavenging') {
			if ($isScavenging > 0) {
				$scavengingSince = $player->actions->scavenge_start;
				$nowDate = new DateTime();
				$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp());
				if ($diff >= 3600) {
					$this->userRepository->getUser($player->id)->actions->update([
						'scavenging' => 0
					]);
					$reward = $this->scavengeReward($diff / 3600, $player->player_stats->level);
					$session = $this->session;
					$section = $session->getSection('returnedScavenging');
					$section->returnedScavenging = true;
					$section->hours = round($diff / 3600);
					$section->money = $reward['money'];
					$section->exp = $reward['xp'];
					$this->flashMessage('You returned from scavenging. You found $' . $reward['money'] . ' and gained ' . $reward['xp'] . ' XP', 'success');
				} else {
					$this->flashMessage('You can return after at least an hour of scavenging', 'danger');
				}
			}
		}
	}

	public function scavengeReward($hours, $level) {
		$totalHours = round($hours);
		$totalReward = 0;
		$totalMoney = 0;
		for($i = 0; $i < $totalHours; $i++) {
			$totalReward += rand(2, 4);
			$totalMoney += rand(2, 5);
		}
		$plusXp = round($this->userRepository->getRewardXp($totalReward, $level));
		$plusMoney = round($this->userRepository->getRewardMoney($totalMoney, $level));
		$this->userRepository->addXp($this->getUser()->identity->id, $plusXp);
		$this->userRepository->addMoney($this->getUser()->identity->id, $totalMoney);
		return [
			'xp' => $plusXp,
			'money' => $plusMoney
		];
	}
}
