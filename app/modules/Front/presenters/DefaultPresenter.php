<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

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
		$player = $this->user->getIdentity();
		if (isset($player->id)) {
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
		} else {
			$drugs = $this->drugsRepository->findAll();
			$this->template->drugs = $drugs;
		}
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
