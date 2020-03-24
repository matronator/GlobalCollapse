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

	public function createComponentIntroForm(): Form
	{
		$avatars = [];
		for ($i = 1; $i <= 21; $i++) {
			$avatars[$i] = $i;
		}
		$form = new Form();
		$form->setHtmlAttribute('id', 'introForm');
		$form->addRadioList('avatar', 'Choose an avatar from the list:', $avatars)
				 ->setDefaultValue(1);
		$form->addHidden('power', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'power')
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
		$selected = $values->avatar;
		$power = intval($values->power);
		$stamina = intval($values->stamina);
		$speed = intval($values->speed);
		$statsTotal = $power + $stamina + $speed;
		if ($statsTotal == 16) {
			if ($selected >= 1 && $selected <= 21) {
				if ($this->player) {
					$this->player->avatar = $selected;
					$this->userRepository->getUser($this->player->id)->update([
						'avatar' => $selected,
						'skillpoints' => 0,
						'tutorial' => 1
					]);
					$this->userRepository->updateStats($this->player->id, $power, $stamina, $speed);
					$this->player->tutorial = 1;
					$this->flashMessage('Intro completed!', 'success');
					$this->redirect('this');
				}
			} else {
				$this->flashMessage('Invalid avatar, please try again.', 'warning');
				$this->redirect('this');
			}
		} else {
			$this->flashMessage('Invalid stats, try again.', 'danger');
			$this->redirect('this');
		}
	}
}
