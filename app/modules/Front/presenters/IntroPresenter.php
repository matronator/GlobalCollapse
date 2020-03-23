<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use DateTime;
use Nette\Application\UI\Form;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class IntroPresenter extends GamePresenter
{

	private $userRepository;

	public function __construct(
		UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
			parent::startup();
			if ($this->userRepository->getUser($this->player->id)->tutorial > 0) {
				$this->redirect('Default:');
			}
	}

	public function renderDefault()
	{
		$this->template->uname = $this->player->username;
		if ($this->player->tutorial == 0) {
			$this->template->disableSidebar = true;
		}
		$avatars = [];
		for ($i = 1; $i <= 21; $i++) {
			$avatars[$i] = $i;
		}
		$this->template->avatars = $avatars;
	}

	public function createComponentAvatarForm(): Form
	{
		$avatars = [];
		for ($i = 1; $i <= 21; $i++) {
			$avatars[$i] = $i;
		}
		$form = new Form();
		$form->addRadioList('avatar', 'Choose an avatar from the list:', $avatars)
				 ->setDefaultValue(1);
		$form->addSubmit('submit', 'Save');
		$form->onSuccess[] = [$this, 'avatarFormSucceeded'];
		return $form;
	}

	public function avatarFormSucceeded(Form $form, $values): void {
		$selected = $values->avatar;
		if ($selected >= 1 && $selected <= 21) {
			if ($this->player) {
				$this->player->avatar = $selected;
				$this->userRepository->getUser($this->player->id)->update([
					'avatar' => $selected
				]);
				$this->flashMessage('Avatar changed', 'success');
				$this->redirect('this');
			}
		}
	}

	private function endTutorial() {
		$this->userRepository->getUser($this->player->id)->update([
			'tutorial' => 1
		]);
		$this->player->tutorial = 1;
		$this->updateStats();
	}

	private function updateStats() {
    $newStats = $this->userRepository->getUser($this->player->id);
		$this->player->power = $newStats->power;
		$this->player->speed = $newStats->speed;
		$this->player->health = $newStats->health;
		$this->player->money = $newStats->money;
		$this->player->skillpoints = $newStats->skillpoints;
		$this->player->energy = $newStats->energy;
		$this->player->energy_max = $newStats->energy_max;
	}
}
