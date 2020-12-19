<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use DateTime;

/////////////////////// ADMIN: DEFAULT PRESENTER ///////////////////////

final class DefaultPresenter extends BasePresenter
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

	/*********************** RENDER VIEWS ***********************/

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

	// Dev tools
	public function actionGenerateVendors()
	{
		if ($this->isAllowed('update')) {
			$names = [
				'HeisenbergDE',
				'happypillz',
				'DutchDeal',
				'StealthPharmacyUK',
				'Apocalypse_drugs',
				'globalist',
				'SunshineExpress',
				'VivaLaCorona',
				'KratomAtomATom',
				'BigBong'
			];
			$i = 1;
			foreach ($names as $name) {
				$primaryData = [];
				$primaryData['name'] = $name;
				$primaryData['base_money'] = (int) round(200000 * 1.5 * $i, -2);
				$primaryData['money'] = $primaryData['base_money'];
				$primaryData['level'] = $i;
				$primaryData['charge'] = round(round(6 - ($i / 3.5), 1) / 100, 3);
				$primaryData['active'] = 1;
				$primaryData['active_since'] = new DateTime();
				$this->drugsRepository->findAllVendors()->insert($primaryData);
				unset($primaryData);
				$i++;
			}
			$this->redirect('Default');
		} else {
			$this->redirect('Default');
		}
	}

	public function actionGenerateOffers()
	{
		if ($this->isAllowed('update')) {
			$drugs = $this->drugsRepository->findAll()->fetchAll();
			$vendors = $this->drugsRepository->findAllVendors()->fetchAll();
			$drugDeck = [];
			foreach ($drugs as $drug) {
				array_push($drugDeck, $drug);
				array_push($drugDeck, $drug);
				array_push($drugDeck, $drug);
				array_push($drugDeck, $drug);
			}
			foreach ($vendors as $vendor) {
				shuffle($drugDeck);
				$chosenDrug = array_pop($drugDeck);
				$quantity = rand(200, 1000) * $vendor->level;
				$limit = (int) round($quantity / 10, 0);
				shuffle($drugDeck);
				$this->drugsRepository->createOffer($vendor->id, $chosenDrug->id, $quantity, $limit);
			}
			$this->redirect('Default:');
		} else {
			$this->redirect('Default:');
		}
	}

	public function actionDarknetUpdate()
	{
		if ($this->isAllowed('update')) {
			$users = $this->userRepository->findAll()->where('money > ?', 500000)->fetchAll();
			foreach ($users as $user) {
				$this->userRepository->findAll()->where('id', $user->id)->update([
					'money' => 500000
				]);
			}
			$this->redirect('Default:');
		} else {
			$this->redirect('Default:');
		}
	}

}
