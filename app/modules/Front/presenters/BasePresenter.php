<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @var Model\PagesRepository */
	private $pages;

	public $contactFormFactory;

	public function injectRepository(
		Model\PagesRepository $pages
	)
	{
		$this->pages = $pages;
	}

	protected function beforeRender()
	{
		$this->template->pages = $this->pages->findAll();
		$this->template->urlAbsolutePath = $this->getURL()->hostUrl;
		$this->template->urlFullDomain = $this->getURL()->host;
		$this->template->defaultLocale = $this->defaultLocale;
		$this->template->user = (object) $this->user->getIdentity();
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
