<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use DateTime;
use Nette\Application\UI\Form;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class BarPresenter extends GamePresenter
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
	}

	public function renderDefault() {
		$player = $this->user->getIdentity();
		$session = $this->session;
		$section = $session->getSection('returned');
		if (isset($section['returned'])) {
			$this->template->returned = true;
			$this->template->hours = $section['hours'];
			$this->template->money = $section['money'];
			$this->template->xpoints = $section['exp'];
			unset($section->returned);
		}
		$isScavenging = $player->scavenging;
		$this->template->scavenging = $isScavenging;
		if ($isScavenging > 0) {
			$scavengingSince = $this->userRepository->getUser($this->player->id)->scavenge_start;
			$this->template->scavengingSince = $scavengingSince;
			$nowDate = new DateTime();
			$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp());
			if ($diff < 3600) {
				$this->template->timePassed = round($diff / 60) . ' minutes';
			} else if ($diff <= 5400) {
				$this->template->timePassed = round($diff / 3600) . ' hour';
			} else {
				$this->template->timePassed = round($diff / 3600) . ' hours';
			}
		}
	}

	public function createComponentMissionsForm(): Form {
		$form = new Form();
		$form->addSubmit('scavenge', 'Go scavenging');
		$form->addSubmit('stopScavenging', 'Return from scavenging');
		$form->onSuccess[] = [$this, 'scavengeFormSucceeded'];
		return $form;
	}

	public function missionsFormSucceeded(Form $form, $values): void {
		$control = $form->isSubmitted();
		$isScavenging = $this->player->scavenging;
		if ($control->name == 'scavenge') {
			if ($isScavenging <= 0) {
				$this->player->scavenging = 1;
				$this->player->scavenge_start = new DateTime();
				$this->userRepository->getUser($this->player->id)->update([
					'scavenging' => $this->player->scavenging,
					'scavenge_start' => $this->player->scavenge_start
				]);
				$this->flashMessage('You went scavenging to the wastelands', 'success');
				$this->redirect('this');
			}
		} else if ($control->name == 'stopScavenging') {
			if ($isScavenging > 0) {
				$scavengingSince = $this->userRepository->getUser($this->player->id)->scavenge_start;
				$nowDate = new DateTime();
				$diff = abs($scavengingSince->getTimestamp() - $nowDate->getTimestamp());
				if ($diff >= 3600) {
					$this->player->scavenging = 0;
					$this->userRepository->getUser($this->player->id)->update([
						'scavenging' => $this->player->scavenging
					]);
					$reward = $this->scavengeReward($diff / 3600);
					$this->flashMessage('You returned from scavenging. You found $' . $reward['money'] . ' and gained ' . $reward['xp'] . ' XP', 'success');
					$session = $this->session;
					$section = $session->getSection('returned');
					$section->returned = true;
					$section->hours = round($diff / 3600);
					$section->money = $reward['money'];
					$section->exp = $reward['xp'];
					$this->redirect('this');
				} else {
					$this->flashMessage('You can return after at least an hour of scavenging', 'danger');
					$this->redirect('this');
				}
			}
		}
	}

	public function scavengeReward($hours) {
		$plusXp = round($hours * rand(5, 10));
		$plusMoney = round($hours * rand(2, 5));
		$this->userRepository->addXp($this->player->id, $plusXp);
		$this->userRepository->getUser($this->player->id)->update([
			'money' => $plusMoney + $this->player->money
			]);
		return [
			'xp' => $plusXp,
			'money' => $plusMoney
		];
	}
}
