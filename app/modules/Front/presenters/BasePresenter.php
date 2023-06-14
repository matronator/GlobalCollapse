<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;
use Nette\Database\Table\ActiveRow;
use stdClass;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @persistent */
	public $locale;

	public $userPrefs;

	/** @var \Contributte\Translation\Translator @inject */
	public $translator;

	/** @var Model\UserRepository */
	protected $userRepository;

	/** @var Model\UnlockablesRepository */
	protected $unlockablesRepository;

	/** @var Model\BuildingsRepository */
	private $buildingsRepository;

    /** @var Model\InventoryRepository */
    private $inventoryRepository;

    /** @var Model\StatisticsRepository */
    protected $statisticsRepository;

	public function injectRepository(
		Model\UserRepository $userRepository,
		Model\UnlockablesRepository $unlockablesRepository,
		Model\BuildingsRepository $buildingsRepository,
        Model\InventoryRepository $inventoryRepository,
        Model\StatisticsRepository $statisticsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->unlockablesRepository = $unlockablesRepository;
		$this->buildingsRepository = $buildingsRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->statisticsRepository = $statisticsRepository;
	}

	protected function beforeRender()
	{
		if ($this->locale) {
			$this->translator->setLocale($this->locale);
		}
		if ($this->isAjax()) {
			$this->redrawControl('main');
			$this->redrawControl('flashes');
		}
		$this->template->urlAbsolutePath = $this->getURL()->hostUrl;
		$this->template->urlFullDomain = $this->getURL()->host;
		$this->template->defaultLocale = $this->defaultLocale;
		$allPlayers = 0;
		$session = $this->session;
		$section = $session->getSection('general-info');
		if (!$section['all_players']) {
			$allPlayers = $this->userRepository->getTotalPlayers();
			$section['all_players'] = $allPlayers;
			$section->setExpiration('300 minutes');
		} else {
			$allPlayers = $section['all_players'];
		}
		$onlinePlayers = $this->userRepository->getOnlinePlayers();
		$this->template->allPlayers = $allPlayers;
		$this->template->onlinePlayers = $onlinePlayers;
		if ($this->user->isLoggedIn()) {
			$user = $this->userRepository->getUser($this->user->getIdentity()->id);
			$this->template->user = $user;
            $gearStats = $this->getGearStats();
            $this->template->gearStats = $gearStats;
            $this->template->gearPower = $gearStats->strength + $gearStats->stamina + $gearStats->speed;
			$gearStats = $this->userRepository->findPlayerGearStats($this->user->getIdentity()->id)->fetch();
			if ($gearStats) {
				$this->template->maxEnergy = $user->player_stats->energy_max + $gearStats->energy_max;
			} else {
				$this->template->maxEnergy = $user->player_stats->energy_max;
			}
			$sectionPrefs = $this->session->getSection('user-prefs');
			$userPreferences = new stdClass();
			if (isset($sectionPrefs->timezone)) {
				$userPreferences->timezone = $sectionPrefs->timezone;
				if (isset($sectionPrefs->dst)) {
					$userPreferences->dst = $sectionPrefs->dst;
				} else {
					$userPreferences->dst = false;
				}
			} else {
				$tz = $this->userRepository->getUserTimezone($this->user->getIdentity()->id);
				if (!$tz->dst || $tz->dst == 0) {
					$userPreferences->dst = false;
				} else {
					$userPreferences->dst = true;
				}
				$userPreferences->timezone = $tz->timezone;
			}
			$this->userPrefs = $userPreferences;

			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$land = $this->buildingsRepository->findPlayerLand($this->user->id)->fetch();
			$this->unlockablesRepository->checkUnlockables($player, $land);
			$newUnlocked = $this->unlockablesRepository->findPlayerUnlocked($this->user->getId())->where('opened', 0)->count();
			$this->template->newUnlocked = $newUnlocked;
		}
		if ($this->user->isLoggedIn() && $this->user->getIdentity()->tutorial === 0 && !$this->presenter->isLinkCurrent('Intro:*')) {
			$this->redirect('Intro:default');
		}
	}

	public function handleChangeLocale(string $locale) {
		if ($this->locale) {
			$this->translator->setLocale($this->locale);
		}
		$this->redirect('this', ['locale' => $locale]);
	}

	public function isAllowed($privilege, $resource = null)
	{
		$resource = $resource ? $resource : $this->name;
		return $this->user->isAllowed($resource, $privilege);
	}

	/**
     * Shortcut translation method
     * @return string
	 * @param mixed $message
	 * @param mixed ...$parameters
	 */
	public function translate($message, ...$parameters): string
    {
		if ($this->locale) {
			$this->translator->setLocale($this->locale);
		}
        return $this->translator->translate($message, $parameters);
    }

	/**
	 * Check if time between `$date` and `$currentDate` is larger than `$targetTime` (defaults to 3600 or 1 hour). Returns the remaining time to `$targetTime` or 0.
	 * @return integer
	 * @param DateTime $date
	 * @param DateTime $currentDate
	 * @param integer $targetTime
	 */
	public static function checkDates(DateTime $date, DateTime $currentDate, int $targetTime = 3600): int
	{
		$diff = abs($date->getTimestamp() - $currentDate->getTimestamp());
		if ($diff >= $targetTime) {
			return 0;
		} else {
			return $targetTime - $diff;
		}
	}

    /**
     * @return object|ActiveRow
     */
    private function getGearStats()
    {
        $gearStats = $this->inventoryRepository->findPlayerGearStats($this->user->id)->fetch();
        if (!$gearStats) {
            return (object)[
                'strength' => 0,
                'stamina' => 0,
                'speed' => 0,
                'attack' => 0,
                'armor' => 0,
                'energy_max' => 0,
                'xp_boost' => 1,
            ];
        }

        return $gearStats;
    }

    public function getUserGearStats(int $userId)
    {
        $gearStats = $this->inventoryRepository->findPlayerGearStats($userId)->fetch();
        if (!$gearStats) {
            return (object)[
                'strength' => 0,
                'stamina' => 0,
                'speed' => 0,
                'attack' => 0,
                'armor' => 0,
                'energy_max' => 0,
                'xp_boost' => 1,
            ];
        }

        return $gearStats;
    }

    public function getUserGearPower(int $userId): int
    {
        $gearStats = $this->getUserGearStats($userId);
        return $gearStats->strength + $gearStats->stamina + $gearStats->speed;
    }
}
