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
	/**
	 * @var array
	 */
	private $allJobs;

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
			} else {
				$whatMission = $player->actions->mission_name;
				$workingUntil = $player->actions->mission_end;
				$now = new DateTime();
				$diff = $workingUntil->getTimestamp() - $now->getTimestamp();
				if ($diff >= 0) {
					$missionKey = array_search($whatMission, array_column($this->allJobs, 'locale'));
					$currentMission = $this->allJobs[$missionKey];
					$missionDuration = intval($currentMission['duration'] * 60);
					$s = $diff % 60;
					$m = $diff / 60 % 60;
					$this->template->minutes = $m > 9 ? $m : '0'.$m;
					$this->template->seconds = $s > 9 ? $s : '0'.$s;
					$this->template->workingUntil = $workingUntil;
					$this->template->timeMax = $missionDuration;
				} else {
					$this->endMission($whatMission);
					$isOnMission = 0;
					$this->redirect('this');
				}
			}
		}
	}

	private function endMission($jobName) {
		$key = array_search($jobName, array_column($this->allJobs, 'locale'));
		$currentJob = $this->allJobs[$key];
		if ($currentJob) {
			$plusXp = $currentJob['xp'];
			$plusMoney = $currentJob['money'];
			$this->userRepository->addXp($this->user->getIdentity()->id, $plusXp);
			$this->userRepository->getUser($this->user->getIdentity()->id)->update([
				'money+=' => $plusMoney
			]);
			$this->userRepository->getUser($this->user->getIdentity()->id)->actions->update([
				'on_mission' => 0
			]);
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
				$key = array_search($value->job, array_column($this->allJobs, 'locale'));
				$chosenJob = $this->allJobs[$key];
				if ($chosenJob) {
					if ($player->player_stats->energy >= $chosenJob['energy']) {
						$now = new DateTime();
						$jobDuration = intval($chosenJob['duration'] * 60);
						// set job end date
						$jobEndTS = $now->getTimestamp();
						$jobEndTS += $jobDuration;
						$now->setTimestamp($jobEndTS);
						$jobEnd = $now->format('Y-m-d H:i:s');
						$missionName = $value->job;
						$this->userRepository->getUser($player->id)->player_stats->update([
							'energy-=' => $chosenJob['energy']
						]);
						$this->userRepository->getUser($player->id)->actions->update([
							'on_mission' => 1,
							'mission_name' => $missionName,
							'mission_end' => $jobEnd
						]);
						// unset sessions
						unset($section);
						unset($session);
						$this->flashMessage('Job accepted', 'success');
						$this->redirect('this');
					} else {
						$this->flashMessage('Not enough energy', 'danger');
						$this->redirect('this');
					}
				} else {
					$this->flashMessage('Something fishy going on...', 'danger');
					$this->redirect('this');
				}
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
