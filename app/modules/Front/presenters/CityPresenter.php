<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use DateTime;
use Nette\Application\UI\Form;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class CityPresenter extends GamePresenter
{

	private $userRepository;
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
		$drugs = $this->drugsRepository->findAll();
		$this->template->drugs = $drugs;
		$this->template->updated = $this->drugsRepository->findDrug(1)->fetch();
		if (isset($this->player->id)) {
			$drugsInventory = $this->drugsRepository->findDrugInventory($this->player->id)->fetchAll();
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
				$userMoney = $this->player->money;
				if ($userMoney >= $totalPrice) {
					$newMoney = $userMoney - $totalPrice;
					$this->userRepository->getUser($this->player->id)->update([
						'money' => $newMoney
						]);
					$this->player->money = $newMoney;
					foreach ($drugs as $drug) {
						$this->drugsRepository->updateUserDrug($this->player->id, $drug->id, $values[$drug->name]);
					}
					$this->flashMessage('Purchase successful', 'success');
					$this->redirect('this');
				} else {
					$missingMoney = $totalPrice - $userMoney;
					$this->flashMessage('Not enough money. You need $' . $missingMoney . ' more', 'danger');
					$this->redirect('this');
				}
			} else if ($control->name === 'sell') {
				$allGood = [];
				$missingDrugs = '';
				$soldDrugs = [];
				foreach($drugs as $drug) {
					if ($values[$drug->name] > 0) {
						$sellDrug = $this->drugsRepository->updateUserDrug($this->player->id, $drug->id, (-1) * ($values[$drug->name]));
						if (!$sellDrug) {
							$missingDrugs = $missingDrugs . $drug->name . ', ';
							array_push($allGood, $drug->name);
						} else {
							array_push($soldDrugs, $drug->name);
							$userMoney = $this->player->money;
							$newMoney = $userMoney + $prices[$drug->name];
							$this->userRepository->getUser($this->player->id)->update([
								'money' => $newMoney
								]);
							$this->player->money = $newMoney;
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
				$this->redirect('this');
			}
		}
	}

	public function renderWastelands() {
		$player = $this->user->getIdentity();
		$isScavenging = $player->scavenging;
		$this->template->scavenging = $isScavenging;
		if ($isScavenging > 0) {
			$scavengingSince = $this->userRepository->getUser($player->id)->scavenge_start;
			$this->template->scavengingSince = $scavengingSince;
			$nowDate = new DateTime('now');
			$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp()) / 60;
			if ($diff < 60) {
				$this->template->timePassed = round($diff, 0) . ' minutes';
			} else {
				$this->template->timePassed = round($diff / 60, 0);
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
		$isScavenging = $this->player->scavenging;
		if ($control->name == 'scavenge') {
			if ($isScavenging <= 0) {
				$this->player->scavenging = 1;
				$this->player->scavenge_start = new DateTime();
				$this->userRepository->getUser($this->player->id)->update([
					'scavenging' => $this->player->scavenging,
					'scavenge_start' => $this->player->scavenge_start
				]);
				$this->flashMessage('You went scavenging to the wastelands', 'success');
				$this->redirect('this');
			}
		} else if ($control->name == 'stopScavenging') {
			if ($isScavenging > 0) {
				$scavengingSince = $this->player->scavenge_start;
				$nowDate = new DateTime('now');
				$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp()) / 60;
				if ($diff >= 60) {
					$this->player->scavenging = 0;
					$this->userRepository->getUser($this->player->id)->update([
						'scavenging' => $this->player->scavenging
					]);
					$reward = $this->scavengeReward($diff / 60);
					$this->flashMessage('You returned from scavenging. You found $' . $reward['money'] . ' and gained ' . $reward['xp'] . ' XP', 'success');
					$this->redirect('this');
				} else {
					$this->flashMessage('You can return after at least an hour of scavenging', 'danger');
					$this->redirect('this');
				}
			}
		}
	}

	public function scavengeReward($hours) {
		$plusXp = round($hours * rand(5, 10));
		$plusMoney = round($hours * rand(2, 5)) + $this->player->money;
		$this->userRepository->addXp($this->player->id, $plusXp);
		$this->userRepository->getUser($this->player->id)->update([
			'money' => $plusMoney
			]);
		$this->player->money = $plusMoney;
		return [
			'xp' => $plusXp,
			'money' => $plusMoney
		];
	}
}
