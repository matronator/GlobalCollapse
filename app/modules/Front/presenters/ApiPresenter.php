<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\UserRepository;
use DateTime;
use Nette\Application\UI\Presenter;

final class ApiPresenter extends Presenter
{
    private $userRepository;
    /**
     * @var array
     */
    private $allJobs;

  public function __construct(
      array $allJobs,
	    UserRepository $userRepository
	)
	{
    parent::__construct();
    $this->allJobs = $allJobs;
    $this->userRepository = $userRepository;
  }

  public function actionJob() {
    $data = [];
    $player = $this->userRepository->getUser($this->user->getIdentity()->id);
    $isOnMission = $player->actions->on_mission;
    if ($isOnMission) {
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
            $minutes = $m > 9 ? $m : '0'.$m;
            $seconds = $s > 9 ? $s : '0'.$s;
            $timeMax = $missionDuration;
            $data = [
                'mission' => true,
                'name' => $whatMission,
                'end' => $workingUntil,
                'duration' => $timeMax,
                'minutes' => $minutes,
                'seconds' => $seconds
            ];
            $this->sendJson($data);
        } else {
            $this->endMission($whatMission);
            $isOnMission = 0;
            $this->flashMessage('Job ended.', 'success');
            $this->sendJson([
                'mission' => false
            ]);
        }
    } else {
        $this->sendJson([
            'mission' => false
        ]);
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
}
