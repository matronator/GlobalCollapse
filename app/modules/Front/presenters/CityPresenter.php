<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use Nette\Forms\Form;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class CityPresenter extends BasePresenter
{

	private UserRepository $userRepository;
	private DrugsRepository $drugsRepository;

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
			if (!$this->user->isLoggedIn())
            $this->redirect('Login:default', ['backlink' => $this->storeRequest()]);
	}

	public function renderDarknet()
	{
		$drugs = $this->drugsRepository->findAll();
		$this->template->drugs = $drugs;
		$drugsInventory = [];
		foreach($drugs as $drug) {
			// $session = $this->session;
			// $section = $session->getSection('price' . $drug->name);
			// $section['price' . $drug->name] = $drug->price;
			$amount = 0;
			$exists = $this->drugsRepository->findUserDrug($this->user->getIdentity()->id, $drug->id)->fetch();
			if ($exists) {
				$amount = $exists->amount;
			}
			array_push($drugsInventory, $amount);
		}
		$this->template->drugsInventory = $drugsInventory;
	}

	public function createComponentDrugsForm(): Form
	{
		$drugs = $this->drugsRepository->findAll();
		$form = new Form();
		$form->setHtmlAttribute('class', 'uk-form-horizontal');
		foreach($drugs as $drug) {
			$form->addInteger($drug->name, $drug->name)
				->setHtmlAttribute('class', 'uk-input uk-form-small')
				->setDefaultValue('0')
				->setHtmlAttribute('min', 0)
				->addRule(Form::INTEGER, 'Input value must be a number');
		}
		$form->addSubmit('buy', 'Buy');
		$form->addButton('sell', 'Sell');
		$form->onSuccess[] = [$this, 'drugsFormSucceeded'];
		return $form;
	}

	public function drugsFormSucceeded(Form $form, $values): void
	{
		$this->restoreRequest($this->backlink);
		$this->redirect('Default:');
		// $control = $form->isSubmitted();
		// $cWeed = intval($values->Weed);
		// $pWeed = intval($this->getSession('priceWeed'));
		// $cEcstasy = intval($values->Ecstasy);
		// $pEcstasy = intval($this->getSession('priceEcstasy'));
		// $cMeth = intval($values->Meth);
		// $pMeth = intval($this->getSession('priceMeth'));
		// $cCoke = intval($values->Coke);
		// $pCoke = intval($this->getSession('priceCoke'));
		// $cHeroin = intval($values->Heroin);
		// $pHeroin = intval($this->getSession('priceHeroin'));
		// if ($control->name === 'buy') {
			// Debugger::log('buy');
			// $userMoney = intval($this->user->getIdentity()->money);
			// Debugger::log($userMoney);
			// $totalPrice = ($pWeed * $cWeed) + ($pEcstasy * $cEcstasy)
			// 	+ ($pMeth * $cMeth) + ($pCoke * $cCoke) + ($pHeroin * $cHeroin);
			// Debugger::log($totalPrice);
			// if ($userMoney >= $totalPrice) {
			// 	Debugger::log('success');
			// 	$this->flashMessage('Nastavení bylo změněno');
			// 	$this->redirect('this');
			// }
		// }
	}
}
