<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\EventsRepository;
use DateTime;
use Nette\Application\UI\Form;
use ActionLocker;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class BarPresenter extends GamePresenter
{

	private $userRepository;
	private $eventRepository;

	public function __construct(
		UserRepository $userRepository,
		EventsRepository $eventRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->eventRepository = $eventRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDefault() {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$actionLocker = new ActionLocker();
		$actionLocker->checkActions($player, $this);
		$this->template->closed = false;
		$activeEvent = $this->eventRepository->getActive()->fetch();
		if ($activeEvent) {
			$activeEventName = $activeEvent->slug;
			if ($activeEventName == 'social-distancing') {
				$this->template->closed = true;
				$this->template->closeReason = $activeEventName;
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
	}
}
