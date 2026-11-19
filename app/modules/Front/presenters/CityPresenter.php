<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\DrugsRepository;
use DateTime;
use Nette\Application\UI\Form;
use ActionLocker;
use App\Services\Darknet\DarknetResponse;
use App\Services\Darknet\DarknetService;
use Nette\Application\UI\Multiplier;
use Nette\Database\Table\ActiveRow;
use Timezones;
use Tracy\Debugger;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class CityPresenter extends GamePresenter
{
	private const OFFER_REFRESH_LIMIT = 3;
	private const OFFER_REFRESH_WINDOW = 8 * 60 * 60;

	public function __construct(
		private DrugsRepository $drugsRepository,
		private DarknetService $darknetService,
	)
	{
		parent::__construct();
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
		$sessionOffers = $this->getSession()->getSection('darknetOffers');
		$sessionOffers->remove();
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

		$offerRefreshState = $this->getOfferRefreshState($player->actions, new DateTime());
		$offerRefreshResetTime = $offerRefreshState['resetIn'] > 0
			? $this->getOfferRefreshResetTime($offerRefreshState['resetIn'])
			: null;
		$this->template->offerRefreshesRemaining = $offerRefreshState['remaining'];
		$this->template->offerRefreshResetLabel = $offerRefreshResetTime
			? $this->translator->translate(
				$this->getOfferRefreshResetTranslationKey('general.darknet.refreshesResetIn', $offerRefreshResetTime),
				$offerRefreshResetTime,
			)
			: null;
	}

	/**
	 * @return array{hours: int, minutes: int}
	 */
	private function getOfferRefreshResetTime(int $seconds): array
	{
		$totalMinutes = (int) ceil($seconds / 60);

		return [
			'hours' => intdiv($totalMinutes, 60),
			'minutes' => $totalMinutes % 60,
		];
	}

	/**
	 * @param array{hours: int, minutes: int} $resetTime
	 */
	private function getOfferRefreshResetTranslationKey(string $baseKey, array $resetTime): string
	{
		if ($resetTime['hours'] === 0) {
			return $baseKey . 'Minutes';
		}

		if ($resetTime['minutes'] === 0) {
			return $baseKey . 'Hours';
		}

		return $baseKey;
	}

	/**
	 * @return array{used: int, remaining: int, resetIn: int, active: bool}
	 */
	private function getOfferRefreshState(ActiveRow $actions, DateTime $now): array
	{
		$used = min(self::OFFER_REFRESH_LIMIT, max(0, (int) $actions->offer_refreshes_used));
		$windowStartedAt = $actions->offer_refreshed;

		if (!$windowStartedAt || $used === 0) {
			return [
				'used' => 0,
				'remaining' => self::OFFER_REFRESH_LIMIT,
				'resetIn' => 0,
				'active' => false,
			];
		}

		$resetIn = self::checkDates($windowStartedAt, $now, self::OFFER_REFRESH_WINDOW);
		if ($resetIn === 0) {
			return [
				'used' => 0,
				'remaining' => self::OFFER_REFRESH_LIMIT,
				'resetIn' => 0,
				'active' => false,
			];
		}

		return [
			'used' => $used,
			'remaining' => self::OFFER_REFRESH_LIMIT - $used,
			'resetIn' => $resetIn,
			'active' => true,
		];
	}

	public function getMaxQuantity(ActiveRow $player, ActiveRow $offer)
	{
		$playerDrugs = $this->drugsRepository->findDrugInventory($player->id)->where('drugs_id', $offer->drug_id)->fetch();
		if ($playerDrugs && $playerDrugs->quantity > $offer->quantity) {
			return $playerDrugs->quantity;
		}
		return $offer->quantity;
	}

	public function actionRefreshOffer(string $hash)
	{
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$now = new DateTime();
		$offerRefreshState = $this->getOfferRefreshState($player->actions, $now);

		if ($offerRefreshState['remaining'] === 0) {
			$offerRefreshResetTime = $this->getOfferRefreshResetTime($offerRefreshState['resetIn']);
			$this->flashMessage($this->translator->translate(
				$this->getOfferRefreshResetTranslationKey('general.messages.warning.cantRefreshOffer', $offerRefreshResetTime),
				$offerRefreshResetTime,
			), 'warning');
			$this->redirect('City:darknet');
		}

		$sessionOffers = $this->getSession()->getSection('darknetOffers');
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
			'quantity' => rand(500, 2000) * ($offer->vendor->level ** 1.05),
			'buys' => 0,
			'sells' => 0
		]);
		$this->userRepository->getUser($player->id)->actions->update([
			'offer_refreshed' => $offerRefreshState['active'] ? $player->actions->offer_refreshed : $now,
			'offer_refreshes_used' => $offerRefreshState['used'] + 1,
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
		$quantity = $values['offerInput'];
		$hash = $values['offerId'];

		$response = match($control->name) {
			'offerBuy' => $this->darknetService->offerBuy($this->user, $hash, $quantity),
			'offerSell' => $this->darknetService->offerSell($this->user, $hash, $quantity),
			default => new DarknetResponse('Something went wrong', 'danger'),
		};

		if ($response->redirect) {
			$this->flashMessage($response->message, $response->type);
			$this->redirect($response->redirect);
		} else {
			$this->flashMessage($response->message, $response->type);
			$this->redrawControl('wrapper');
			$this->redrawControl('flashes');
			$this->redrawControl('sidebarStats');
		}
	}

	public function renderWastelands() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$actionLocker = new ActionLocker();
		$actionLocker->checkActions($player, $this);
		$session = $this->getSession();
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
		if ($control->name === 'scavenge') {
			if ($isScavenging <= 0 && $isOnMission <= 0) {
				$playerScavengeStart = new DateTime();
				$this->userRepository->getUser($player->id)->actions->update([
					'scavenging' => 1,
					'scavenge_start' => $playerScavengeStart
				]);
				$this->flashMessage($this->translate('general.messages.success.scavengingStart'), 'success');
			}
		} else if ($control->name === 'stopScavenging') {
			if ($isScavenging > 0) {
				$scavengingSince = $player->actions->scavenge_start;
				$nowDate = new DateTime();
				$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp());
				if ($diff >= 3600) {
					$this->userRepository->getUser($player->id)->actions->update([
						'scavenging' => 0
					]);
					$reward = $this->scavengeReward($diff / 3600, $player->player_stats->level);
					$session = $this->getSession();
					$section = $session->getSection('returnedScavenging');
					$section->returnedScavenging = true;
					$section->hours = round($diff / 3600);
					$section->money = $reward['money'];
					$section->exp = $reward['xp'];
                    $this->statisticsRepository->findByUser($player->id)->update([
                        'minutes_scavenged+=' => (int) date('i', $diff),
                        'money_from_scavenging+=' => $reward['money'],
                        'times_scavenged+=' => 1,
                    ]);
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
			'xp' => $plusXp * $this->getUserGearStats($this->player->id)->xp_boost,
			'money' => $plusMoney
		];
	}
}
