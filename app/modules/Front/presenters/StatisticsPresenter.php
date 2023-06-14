<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette\Database\Table\ActiveRow;

final class StatisticsPresenter extends BasePresenter
{
    private ActiveRow $player;
    private ActiveRow $statistics;

    public function __construct()
    {
        parent::__construct();
    }

    protected function startup()
    {
        parent::startup();

        $this->player = $this->userRepository->getUser($this->user->getIdentity()->id);
        if (!$this->player) {
            $this->redirect('Default:default');
        }

        $this->statistics = $this->statisticsRepository->findByUser($this->player->id);
        if (!$this->statistics) {
            $this->redirect('Default:default');
        }
    }

    public function renderDefault()
    {
        $this->template->statistics = $this->statistics;
        $this->template->moneySources = $this->getMoneySourcesData();
        $this->template->timeSpent = $this->getTimeSpentData();
        $this->template->activitiesCount = $this->getActivitiesCountData();
    }

    public function actionGetMoneySourceChartData(?string $username = null)
    {
        if (!$this->user->isLoggedIn()) {
            $this->sendJson(['error' => 'Not logged in.']);
        }

        $data = $this->getMoneySourcesData($username);

        $this->sendJson($data);
    }

    public function actionGetTimeSpentData(?string $username = null)
    {
        if (!$this->user->isLoggedIn()) {
            $this->sendJson(['error' => 'Not logged in.']);
        }

        $data = $this->getTimeSpentData($username);

        $this->sendJson($data);
    }

    public function actionGetActivitiesCountData(?string $username = null)
    {
        if (!$this->user->isLoggedIn()) {
            $this->sendJson(['error' => 'Not logged in.']);
        }

        $data = $this->getActivitiesCountData($username);

        $this->sendJson($data);
    }

    private function getMoneySourcesData(?string $username = null): object
    {
        if (!$username) {
            return (object) [
                'jobs' => (int) $this->statistics->money_from_jobs,
                'darknet' => (int) $this->statistics->money_from_darknet,
                'scavenging' => (int) $this->statistics->money_from_scavenging,
                'assaults' => (int) $this->statistics->money_from_assaults,
                'market' => (int) $this->statistics->money_from_market,
            ];
        }

        $stats = $this->statisticsRepository->findByUser($this->userRepository->getUserByName($username)->id);
        return (object) [
            'jobs' => (int) $stats->money_from_jobs,
            'darknet' => (int) $stats->money_from_darknet,
            'scavenging' => (int) $stats->money_from_scavenging,
            'assaults' => (int) $stats->money_from_assaults,
            'market' => (int) $stats->money_from_market,
        ];
    }

    private function getTimeSpentData(?string $username = null): object
    {
        if (!$username) {
            return (object) [
                'jobs' => (int) $this->statistics->minutes_on_job,
                'resting' => (int) $this->statistics->minutes_rested,
                'scavenging' => (int) $this->statistics->minutes_scavenged,
            ];
        }

        $stats = $this->statisticsRepository->findByUser($this->userRepository->getUserByName($username)->id);
        return (object) [
            'jobs' => (int) $stats->minutes_on_job,
            'resting' => (int) $stats->minutes_rested,
            'scavenging' => (int) $stats->minutes_scavenged,
        ];
    }

    private function getActivitiesCountData(?string $username = null): object
    {
        if (!$username) {
            return (object) [
                'jobs' => (int) $this->statistics->jobs_completed,
                'resting' => (int) $this->statistics->times_rested,
                'scavenging' => (int) $this->statistics->times_scavenged,
            ];
        }

        $stats = $this->statisticsRepository->findByUser($this->userRepository->getUserByName($username)->id);
        return (object) [
            'jobs' => (int) $stats->jobs_completed,
            'resting' => (int) $stats->times_rested,
            'scavenging' => (int) $stats->times_scavenged,
        ];
    }
}
