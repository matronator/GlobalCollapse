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

	private $userRepository;
	private $drugsRepository;

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
		$player = $this->user->getIdentity();
		if (isset($player->id)) {
			$drugsInventory = $this->drugsRepository->findDrugInventory($player->id)->fetchAll();
			if (count($drugsInventory) > 0) {
				$this->template->drugsInventory = $drugsInventory;
			} else {
				$drugs = $this->drugsRepository->findAll();
				$this->template->drugs = $drugs;
			}
		} else {
			$drugs = $this->drugsRepository->findAll();
			$this->template->drugs = $drugs;
		}
	}
}
