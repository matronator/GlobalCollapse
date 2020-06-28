<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use DateTime;
use Nette\Application\UI\Form;
use ActionLocker;
use Nette\Application\UI\Multiplier;
use VendorOfferForm;
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

	public function renderAlphabay() {
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
		}
		$this->template->playerDrugs = $playerDrugs;
		// Vendors
		$vendors = $this->drugsRepository->findAllVendors()->where('active', 1);
		$this->template->vendors = $vendors;
		$offers = $this->drugsRepository->findAvailableOffers($player->player_stats->level)->where('vendor_offers.active', 1);
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
	}

	protected function createComponentVendorOfferForm(): Multiplier
	{
		$multi = new Multiplier(function ($offerId) {
      $form = new Form;
      $form->addInteger('offerInput' . $offerId)
           ->setHtmlAttribute('class', 'uk-input darknet-offer-input')
           ->setHtmlId('offerInput' . $offerId)
           ->setHtmlAttribute('placeholder', 'Enter amount')
					 ->setRequired();

			$form->addSubmit('offerBuy' . $offerId, 'Buy')
					 ->setHtmlId('offerBuy' . $offerId)
					 ->setHtmlAttribute('class', 'uk-button uk-button-small uk-button-primary')
					 ->setHtmlAttribute('data-offer-button', 'buy');

			$form->addSubmit('offerSell' . $offerId, 'Sell')
					 ->setHtmlId('offerSell' . $offerId)
					 ->setHtmlAttribute('class', 'uk-button uk-button-small uk-button-danger')
					 ->setHtmlAttribute('data-offer-button', 'sell');

			return $form;

      // $form->onSuccess[] = [$this, 'processForm'];
    });
    return $multi;
	}

	public function actionOfferBuy(string $hash = null, int $quantity = null)
	{
		if ($hash == null || $quantity == null) {
			$this->flashMessage($this->translator->translate('general.messages.danger.somethingFishy'), 'danger');
			$this->redirect('City:alphabay');
		} else {
			$sessionOffers = $this->session->getSection('darknetOffers');
			$oldOfferId = $sessionOffers[$hash];
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$offer = $this->drugsRepository->findOffer($oldOfferId)->fetch();
			if (is_object($offer)) {
				if ($offer->active) {
					if ($offer->quantity >= $quantity) {
						$totalPrice = (int) round(($quantity * $offer->drug->price) * 1.05, 0);
						if ($player->money >= $totalPrice) {
							$this->drugsRepository->offerBuy($offer->id, $player->id, $quantity);
							$this->userRepository->addMoney($player->id, -$totalPrice);
							$this->flashMessage($this->translator->translate('general.messages.success.purchaseSuccessful'), 'success');
							$this->redirect('City:alphabay');
						} else {
							$this->flashMessage($this->translator->translate('general.messages.danger.notEnoughMoney'), 'danger');
							$this->redirect('City:alphabay');
						}
					} else {
						$this->flashMessage($this->translator->translate('general.messages.danger.orderBuyTooMany'), 'danger');
						$this->redirect('City:alphabay');
					}
				} else {
					$this->flashMessage($this->translator->translate('general.messages.danger.somethingFishy'), 'danger');
					$this->redirect('City:alphabay');
				}
			} else {
				$this->flashMessage($this->translator->translate('general.messages.danger.somethingFishy'), 'danger');
				$this->redirect('City:alphabay');
			}
		}
	}

	public function renderDarknet()
	{
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$actionLocker = new ActionLocker();
		$actionLocker->checkActions($player, $this);
		$drugs = $this->drugsRepository->findAll()->fetchAll();
		$this->template->drugs = $drugs;
		$updated = $drugs['1']->updated;
		$this->template->updated = Timezones::getUserTime($updated, $this->userPrefs->timezone);
		$now = new DateTime();
		$diff = abs($updated->getTimestamp() - $now->getTimestamp());
		if ($diff < 3600) {
			$this->template->timeAgo = round($diff / 60) . ' minutes';
		} else if ($diff <= 5400) {
			$this->template->timeAgo = round($diff / 3600) . ' hour';
		} else {
			$this->template->timeAgo = round($diff / 3600) . ' hours';
		}
		$drugsInventory = $this->drugsRepository->findDrugInventory($player->id)->order('drugs_id', 'ASC')->fetchAll();
		if (count($drugsInventory) > 0) {
			$this->template->drugsInventory = $drugsInventory;
		}

		foreach($drugs as $drug) {
			$session = $this->session;
			$section = $session->getSection('price' . $drug->name);
			$section['price' . $drug->name] = $drug->price;
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

	public function createComponentDarknetForm(): Form
	{
		$drugs = $this->drugsRepository->findAll();
		$form = new Form();
		$form->setHtmlAttribute('class', 'uk-form-horizontal');
		foreach($drugs as $drug) {
			$form->addInteger($drug->name, $drug->name)
				->setHtmlAttribute('class', 'uk-input input-number')
				->setHtmlAttribute('min', 0)
				->setHtmlAttribute('data-drug-input', $drug->name)
				->setHtmlAttribute('data-price', $drug->price)
				->setHtmlId($drug->name)
				->setDefaultValue('0')
				->addRule(Form::INTEGER, 'Input value must be a number');
		}
		$form->addSubmit('buy', 'Buy');
		$form->addSubmit('sell', 'Sell');
		$form->onSuccess[] = [$this, 'darknetFormSucceeded'];
		return $form;
	}

	public function darknetFormSucceeded(Form $form, $values): void
	{
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$control = $form->isSubmitted();
		$prices = [];
		$drugs = $this->drugsRepository->findAll();
		foreach($drugs as $drug) {
			$session = $this->session;
			$section = $session->getSection('price' . $drug->name);
			$prices[$drug->name] = $section['price' . $drug->name] * $values[$drug->name];
		}
		$totalPrice = array_sum($prices);
		if ($totalPrice > 0) {
			if ($control->name === 'buy') {
				if ($player->money >= $totalPrice) {
					foreach ($drugs as $drug) {
						if ($values[$drug->name] > 0) {
							$this->drugsRepository->buyDrugs($player->id, $drug->id, $values[$drug->name]);
							$this->userRepository->addMoney($player->id, -$prices[$drug->name]);
						}
					}
					$this->flashMessage($this->translator->translate('general.messages.success.purchaseSuccessful'), 'success');
				} else {
					$this->flashMessage($this->translator->translate('general.messages.danger.notEnoughMoney'), 'danger');
				}
			} else if ($control->name === 'sell') {
				$allGood = [];
				$missingDrugs = '';
				$soldDrugs = [];
				foreach($drugs as $drug) {
					if ($values[$drug->name] > 0) {
						$sellDrug = $this->drugsRepository->sellDrug($player->id, $drug->id, $values[$drug->name]);
						if (!$sellDrug) {
							$missingDrugs = $missingDrugs . $drug->name . ', ';
							array_push($allGood, $drug->name);
						} else {
							array_push($soldDrugs, $drug->name);
							$this->userRepository->addMoney($player->id, $prices[$drug->name]);
						}
					}
				}
				if (count($allGood) > 0 && count($soldDrugs) > 0) {
					$this->flashMessage('You haven\'t sold ' . $missingDrugs . 'because you don\'t have enough. Other drugs sold succesfully.', 'warning');
				} else if (count($allGood) > 0 && count($soldDrugs) === 0) {
					$this->flashMessage('You can\'t sell drugs you don\'t have.', 'danger');
				} else if (count($allGood) === 0 && count($soldDrugs) > 0) {
					$this->flashMessage($this->translate('general.messages.success.drugsSold'), 'success');
				}
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
