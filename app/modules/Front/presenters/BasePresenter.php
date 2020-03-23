<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @var Model\PagesRepository */
	private $pages;

	/** @var Model\UserRepository */
	private $userRepository;

	public $contactFormFactory;

	public function injectRepository(
		Model\PagesRepository $pages,
		Model\UserRepository $userRepository
	)
	{
		$this->pages = $pages;
		$this->userRepository = $userRepository;
	}

	protected function beforeRender()
	{
		$this->template->pages = $this->pages->findAll();
		$this->template->urlAbsolutePath = $this->getURL()->hostUrl;
		$this->template->urlFullDomain = $this->getURL()->host;
		$this->template->defaultLocale = $this->defaultLocale;
		$this->template->user = (object) $this->user->getIdentity();
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
