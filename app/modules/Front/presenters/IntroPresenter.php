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
			if ($this->user->getIdentity()->tutorial !== 0) {
				$this->redirect('Default:default');
			}
	}

	public function renderDefault()
	{
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$this->template->uname = $player->username;
		if ($player->tutorial == 0) {
			$this->template->disableSidebar = true;
		}
		$avatars = [];
		for ($i = 1; $i <= DefaultPresenter::AVATAR_COUNT; $i++) {
			$avatars[$i] = $i;
		}
		$this->template->avatars = $avatars;
	}

	public function createComponentIntroForm(): Form
	{
		$avatars = [];
		for ($i = 1; $i <= DefaultPresenter::AVATAR_COUNT; $i++) {
			$avatars[$i] = $i;
		}
		$form = new Form();
		$form->setHtmlAttribute('id', 'introForm');
		$form->addRadioList('avatar', 'Choose an avatar from the list:', $avatars)
				 ->setDefaultValue(1);
		$form->addHidden('strength', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'strength')
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('stamina', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'stamina')
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('speed', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'speed')
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->onSuccess[] = [$this, 'introFormSucceeded'];
		return $form;
	}

	public function introFormSucceeded(Form $form, $values): void {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$selected = $values->avatar;
		$strength = intval($values->strength);
		$stamina = intval($values->stamina);
		$speed = intval($values->speed);
		$statsTotal = $strength + $stamina + $speed;
		if ($statsTotal == 16) {
			if ($selected >= 1 && $selected <= DefaultPresenter::AVATAR_COUNT) {
				if ($player) {
					$this->userRepository->getUser($player->id)->update([
						'avatar' => $selected . ".jpg",
						'skillpoints' => 0,
						'tutorial' => 1
					]);
					$this->userRepository->updateStats($player->id, $strength, $stamina, $speed);
					$this->user->getIdentity()->tutorial = 1;
					$this->flashMessage('Intro completed!', 'success');
					$this->redirect('Default:default');
				}
			} else {
				$this->flashMessage('Invalid avatar, please try again.', 'warning');
			}
		} else {
			$this->flashMessage('Invalid stats, try again.', 'danger');
		}
		$this->redirect('this');
	}
}
