<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use DateTime;
use Nette\Application\UI\Form;
use ActionLocker;

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

	public function renderDarknet()
	{
		$newStats = $this->userRepository->getUser($this->user->getIdentity()->id);
		$actionLocker = new ActionLocker();
		$actionLocker->checkActions($newStats, $this);
		$drugs = $this->drugsRepository->findAll();
		$this->template->drugs = $drugs;
		$this->template->updated = $this->drugsRepository->findDrug(1)->fetch();
		if (isset($this->getUser()->identity->id)) {
			$drugsInventory = $this->drugsRepository->findDrugInventory($this->getUser()->identity->id)->order('drugs_id', 'ASC')->fetchAll();
			if (count($drugsInventory) > 0) {
				$this->template->drugsInventory = $drugsInventory;
			}
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
			$this->template->scavengingSince = $scavengingSince;
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
							$this->userRepository->getUser($player->id)->update([
								'money-=' => $prices[$drug->name]
							]);
						}
					}
					$this->flashMessage('Purchase successful', 'success');
				} else {
					$this->flashMessage('Not enough money', 'danger');
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
							$this->userRepository->getUser($player->id)->update([
								'money+=' => $prices[$drug->name]
							]);
						}
					}
				}
				if (count($allGood) > 0 && count($soldDrugs) > 0) {
					$this->flashMessage('You haven\'t sold ' . $missingDrugs . 'because you don\'t have enough. Other drugs sold succesfully.', 'warning');
				} else if (count($allGood) > 0 && count($soldDrugs) === 0) {
					$this->flashMessage('You can\'t sell drugs you don\'t have.', 'danger');
				} else if (count($allGood) === 0 && count($soldDrugs) > 0) {
					$this->flashMessage('Drugs successfully sold.', 'success');
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
				$this->flashMessage('You went scavenging to the wastelands', 'success');
			}
		} else if ($control->name == 'stopScavenging') {
			if ($isScavenging > 0) {
				$scavengingSince = $this->userRepository->getUser($player->id)->actions->scavenge_start;
				$nowDate = new DateTime();
				$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp());
				if ($diff >= 3600) {
					$this->userRepository->getUser($player->id)->actions->update([
						'scavenging' => 0
					]);
					$reward = $this->scavengeReward($diff / 3600, $player->player_stats->level);
					$this->flashMessage('You returned from scavenging. You found $' . $reward['money'] . ' and gained ' . $reward['xp'] . ' XP', 'success');
					$session = $this->session;
					$section = $session->getSection('returnedScavenging');
					$section->returnedScavenging = true;
					$section->hours = round($diff / 3600);
					$section->money = $reward['money'];
					$section->exp = $reward['xp'];
					$this->redirect('this');
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
		$this->userRepository->getUser($this->getUser()->identity->id)->update([
			'money+=' => $totalMoney
		]);
		return [
			'xp' => $plusXp,
			'money' => $plusMoney
		];
	}
}
