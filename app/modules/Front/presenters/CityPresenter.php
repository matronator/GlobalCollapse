<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
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
					$this->redirect('City:darknet');
				}
			} else if ($control->name === 'sell') {
				$this->redirect('this');
			}
		}
	}
}
