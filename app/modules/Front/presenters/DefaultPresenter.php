<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;


/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class DefaultPresenter extends BasePresenter
{
	/** @var Model\ArticlesRepository */
	private $articles;

	private UserRepository $userRepository;
	private DrugsRepository $drugsRepository;

	public function __construct(
		UserRepository $userRepository,
		DrugsRepository $drugsRepository,
		Model\ArticlesRepository $articles
	)
	{
		$this->articles = $articles;
		$this->userRepository = $userRepository;
		$this->drugsRepository = $drugsRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDefault()
	{
		$this->template->articles = $this->articles->findAll();
		$drugs = $this->drugsRepository->findAll();
		$this->template->drugs = $drugs;
		$drugsInventory = [];
		foreach($drugs as $drug) {
			$amount = 0;
			$exists = $this->drugsRepository->findUserDrug($this->user->getIdentity()->id, $drug->id);
			if ($exists) {
				$amount = $exists->amount;
			}
			array_push($drugsInventory, $amount);
		}
		$this->template->drugsInventory = $drugsInventory;
	}
}
