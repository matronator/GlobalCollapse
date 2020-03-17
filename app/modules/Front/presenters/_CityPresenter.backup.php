<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;

/////////////////////// FRONT: City PRESENTER ///////////////////////

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

	public function renderDarknet(): void
	{
		$drugs = $this->drugsRepository->findAll();
		$drugInventory = $this->drugsRepository->findUserDrug($this->user->getIdentity()->id);
		$this->template->drugs = $drugs;
		$this->template->drugInventory = $drugInventory;
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
				->setHtmlAttribute('class', 'uk-input uk-form-small')
				->setHtmlAttribute('min', 0)
				->setDefaultValue('0')
				->addRule(Form::INTEGER, 'Input value must be a number');
		}
		$form->addSubmit('buy', 'Buy');

		$form->addButton('sell', 'Sell');

		$form->onSuccess[] = [$this, 'darknetFormSucceeded'];

		return $form;
	}

	public function darknetFormSucceeded(Form $form, ArrayHash $values): void
	{
		$control = $form->isSubmitted();
		$session = $this->session;
		$cWeed = intval($values->Weed);
		$pWeed = intval($session->getSection('priceWeed')['priceWeed']);
		$cEcstasy = intval($values->Ecstasy);
		$pEcstasy = intval($session->getSection('priceEcstasy')['priceEcstasy']);
		$cMeth = intval($values->Meth);
		$pMeth = intval($session->getSection('priceMeth')['priceMeth']);
		$cCoke = intval($values->Coke);
		$pCoke = intval($session->getSection('priceCoke')['priceCoke']);
		$cHeroin = intval($values->Heroin);
		$pHeroin = intval($session->getSection('priceHeroin')['priceHeroin']);
		$userId = $this->user->getIdentity()->id;
		if ($control->name === 'buy') {
			$userMoney = intval($this->user->getIdentity()->money);
			$totalPrice = ($pWeed * $cWeed) + ($pEcstasy * $cEcstasy)
				+ ($pMeth * $cMeth) + ($pCoke * $cCoke) + ($pHeroin * $cHeroin);
			if ($userMoney >= $totalPrice) {
				$i = 1;
				unset($values['sell']);
				unset($values['buy']);
				foreach($values as $value) {
					$this->drugsRepository->updateUserDrug($userId, $i, $value);
					$i+=1;
				}
			}
		}
	}
}
