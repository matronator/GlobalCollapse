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
	private $jobs = [];

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
		} else {
			$allJobs = $this->context->parameters['jobs'];
			for ($i = 0; $i < 3; $i++) {
				$selectedJob = $this->getRandomWeightedElement($allJobs);
				$jobKey = array_search($selectedJob, $allJobs);
				array_push($this->jobs, $selectedJob);
				unset($allJobs[$jobKey]);
			}
			$this->template->jobs = $this->jobs;
		}
	}

	public function createComponentJobsForm(): Form {
		$form = new Form();
		$jobsRadio = [];
		foreach($this->jobs as $job) {
			$jobsRadio[$job['locale']] = $job['locale'];
		}
		$form->addRadioList('job', 'Select a job', $jobsRadio);
		$form->addSubmit('scavenge', 'Go scavenging');
		$form->addSubmit('stopScavenging', 'Return from scavenging');
		$form->onSuccess[] = [$this, 'jobsFormSucceeded'];
		return $form;
	}

	public function jobsFormSucceeded(Form $form, $values): void {

	}

	private function getRandomWeightedElement(array $weightedValues) {
    $rand = rand(1, (int) array_sum(array_column($weightedValues, 'droprate')));
    foreach ($weightedValues as $key) {
      $rand -= $key['droprate'];
      if ($rand <= 0) {
        return $key;
      }
    }
  }
}
