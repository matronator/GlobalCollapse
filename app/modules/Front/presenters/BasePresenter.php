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

	public $contactFormFactory;

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
		$this->template->user = (object) $this->user->getIdentity();
		$this->template->allPlayers = $this->userRepository->getTotalPlayers();
		$this->template->onlinePlayers = $this->userRepository->getOnlinePlayers();
		if ($this->user->isLoggedIn()) {
			$this->template->user = $this->userRepository->getUser($this->user->getIdentity()->id);
			$player = $this->user->getIdentity();
      $this->userRepository->getUser($player->id)->update([
				'last_active' => new DateTime()
			]);
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
