<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use DateTime;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @persistent */
	public $locale;

	/** @var \Kdyby\Translation\Translator @inject */
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
		$section = $session->getSection('general');
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
