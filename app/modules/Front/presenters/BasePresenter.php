<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use DateTime;
use stdClass;
use Timezones;

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
	private $userRepository;

	public function injectRepository(
		Model\UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	protected function beforeRender()
	{
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
			$this->template->user = $this->userRepository->getUser($this->user->getIdentity()->id);
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
		}
		if ($this->user->isLoggedIn() && $this->user->getIdentity()->tutorial === 0 && !$this->presenter->isLinkCurrent('Intro:*')) {
			$this->redirect('Intro:default');
		}
	}

	public function handleChangeLocale(string $locale) {
		$this->redirect('this', ['locale' => $locale]);
	}

	public function isAllowed($privilege, $resource = null)
	{
			$resource = $resource ? $resource : $this->name;
			return $this->user->isAllowed($resource, $privilege);
	}

}
