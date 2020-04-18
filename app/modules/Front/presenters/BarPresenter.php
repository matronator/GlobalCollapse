<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\EventsRepository;
use DateTime;
use Nette\Application\UI\Form;
use ActionLocker;
use Tracy\Debugger;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class BarPresenter extends GamePresenter
{

	private $userRepository;
	private $eventRepository;
	/**
	 * @var array
	 */
	private array $allJobs;
	private array $jobs = [];

	public function __construct(
		array $allJobs,
		UserRepository $userRepository,
		EventsRepository $eventRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->eventRepository = $eventRepository;
		$this->allJobs = $allJobs;
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
			$isOnMission = $player->actions->on_mission;
			$this->template->onMission = $isOnMission;
			if (!$isOnMission) {
				$session = $this->session;
				$section = $session->getSection('jobs');
				$section->setExpiration('60 minutes');
				$templateJobs = [];
				if ($section['shown'] != true) {
					$jobDeck = $this->allJobs;
					for ($i = 0; $i < 3; $i++) {
						$selectedJob = $this->getRandomWeightedElement($jobDeck);
						$section['job-' . $i] = $selectedJob;
						array_push($templateJobs, $selectedJob);
						$jobKey = array_search($selectedJob, $jobDeck);
						unset($jobDeck[$jobKey]);
					}
					$section['shown'] = true;
				} else {
					for ($i = 0; $i < 3; $i++) {
						$selectedJob = $section['job-' . $i];
						array_push($templateJobs, $selectedJob);
					}
				}
				$this->template->jobs = $templateJobs;
			}
		}
	}

	public function createComponentJobsForm(): Form {
		$form = new Form();
		$form->setHtmlAttribute('id', 'jobsForm');
		$jobsRadio = [];
		$session = $this->session;
		$section = $session->getSection('jobs');
		for ($i = 0; $i < 3; $i++) {
			$jobFromSess = $section['job-' . $i];
			$jobsRadio[$jobFromSess['locale']] = $jobFromSess['locale'];
		}
		$form->addRadioList('job', 'Select a job:', $jobsRadio)
				->setRequired();
		$form->addSubmit('work', 'Accept job');
		$form->onSuccess[] = [$this, 'jobsFormSucceeded'];
		return $form;
	}

	public function jobsFormSucceeded(Form $form, $value): void {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$isOnMission = $player->actions->on_mission;
		$isScavenging = $player->actions->scavenging;
		$isResting = $player->actions->resting;
		if (!$isOnMission && !$isScavenging && !$isResting) {
			$availableJobs = [];
			$session = $this->session;
			$section = $session->getSection('jobs');
			for ($i = 0; $i < 3; $i++) {
				$jobFromSess = $section['job-' . $i];
				$availableJobs[$jobFromSess['locale']] = $jobFromSess['locale'];
			}
			if (in_array($value->job, $availableJobs)) {
				$missionStart = new DateTime();
				$missionName = $value->job;
				$this->userRepository->getUser($player->id)->actions->update([
					'on_mission' => 1,
					'mission_name' => $missionName,
					'mission_start' => $missionStart
				]);
				$this->flashMessage('Job accepted', 'success');
				$this->redirect('this');
			} else {
				$this->flashMessage('Something fishy going on...', 'danger');
				$this->redirect('this');
			}
		}
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
