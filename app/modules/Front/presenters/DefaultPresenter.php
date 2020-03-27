<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use DateTime;

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
			if ($this->user->isLoggedIn()) {
				$player = $this->userRepository->getUser($this->user->getIdentity()->id);
				if ($player->tutorial == 0) {
					$this->redirect('Intro:');
				}
			}
	}

	public function renderDefault()
	{
		$this->template->articles = $this->articles->findAll();
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$this->template->user = $player;
			$avatars = [];
			for ($i = 1; $i <= 21; $i++) {
				$avatars[$i] = $i;
			}
			$newStats = $this->userRepository->getUser($player->id);
			$this->template->avatars = $avatars;
			$this->template->userAvatar = $player->avatar;
			$xp = $newStats->player_stats->xp;
			$this->template->xp = $xp;
			$xpMax = $newStats->player_stats->xp_max;
			$xpMin = $newStats->player_stats->xp_min;
			$this->template->xpMax = $xpMax;
			$this->template->progressValue = round((($xp - $xpMin) / ($xpMax - $xpMin)) * (100));

			$drugsInventory = $this->drugsRepository->findDrugInventory($player->id)->order('drugs_id', 'ASC')->fetchAll();
			if (count($drugsInventory) > 0) {
				$this->template->drugsInventory = $drugsInventory;
			} else {
				$drugs = $this->drugsRepository->findAll();
				$this->template->drugs = $drugs;
			}

			// Leaderboard
			$lastPage = 0;
			$page = 1;
			$leaderboard = $this->userRepository->findUsers()->page($page, 10, $lastPage);
			$this->template->users = $leaderboard;
			$this->template->lastPage = $lastPage;
			$this->template->page = $page;
		} else {
			$drugs = $this->drugsRepository->findAll();
			$this->template->drugs = $drugs;
		}
	}

	public function renderTraining()
	{
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$newStats = $this->userRepository->getUser($player->id);
			if ($newStats->scavenging == 1) {
				$this->redirect('City:wastelands');
			}
			$this->template->user = $newStats;
			$xp = $newStats->player_stats->xp;
			$xpMax = $newStats->player_stats->xp_max;
			$xpMin = $newStats->player_stats->xp_min;
			$this->template->skillpoints = $newStats->skillpoints;
			$this->template->progressValue = round((($xp - $xpMin) / ($xpMax - $xpMin)) * (100));
			$this->template->isTraining = $newStats->training;
		} else {
			$this->redirect('Login:default');
		}
	}

	public function createComponentTrainingForm(): Form {
		$form = new Form();
		$form->setHtmlAttribute('id', 'trainingForm');
		$form->addSubmit('strength', 'Train');
		$form->addSubmit('stamina', 'Train');
		$form->addSubmit('speed', 'Train');
		$form->onSuccess[] = [$this, 'skillpointsFormSucceeded'];
		return $form;
	}

	public function trainingFormSucceeded(Form $form, $values): void {
		$control = $form->isSubmitted();
		$trainNumber = 0;
		switch ($control->name) {
			case 'strength':
				$trainNumber = 1;
			break;
			case 'stamina':
				$trainNumber = 2;
			break;
			case 'speed':
				$trainNumber = 3;
			break;
			default:
				$trainNumber = 0;
		}
		if ($this->user->training == 0 && $trainNumber != 0) {
			$trainingStart = new DateTime();
			$this->user->training_start = $trainingStart;
			$this->userRepository->getUser($this->user->getIdentity()->id)->update([
				'training' => $trainNumber,
				'training_start' => $trainingStart
			]);
		}
	}

	public function createComponentSkillpointsForm(): Form {
		$form = new Form();
		$form->setHtmlAttribute('id', 'skillpointsForm');
		$form->addHidden('strength', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'strength')
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('stamina', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'stamina')
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('speed', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'speed')
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->onSuccess[] = [$this, 'skillpointsFormSucceeded'];
		return $form;
	}

	public function skillpointsFormSucceeded(Form $form, $values): void {

	}

	public function createComponentAvatarForm(): Form
	{
		$avatars = [];
		for ($i = 1; $i <= 21; $i++) {
			$avatars[$i] = $i;
		}
		$form = new Form();
		$form->addRadioList('avatar', 'Choose an avatar from the list:', $avatars);
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = [$this, 'avatarFormSucceeded'];
		return $form;
	}

	public function avatarFormSucceeded(Form $form, $values): void {
		$selected = $values->avatar;
		if ($selected >= 1 && $selected <= 21) {
			$player = $this->user->getIdentity();
			if ($player) {
				$player->avatar = $selected;
				$this->userRepository->getUser($player->id)->update([
					'avatar' => $selected
				]);
				$this->flashMessage('Avatar changed', 'success');
				$this->redirect('this');
			}
		}
	}
}
