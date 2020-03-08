<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\FrontModule\Factories\ContactFormFactory;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @var Model\PagesRepository */
	private $pages;

	public $contactFormFactory;

	public function injectRepository(
		Model\PagesRepository $pages,
		ContactFormFactory $contactFormFactory
	)
	{
		$this->pages = $pages;
		$this->contactFormFactory = $contactFormFactory;
	}

	protected function beforeRender()
	{
		$this->template->pages = $this->pages->findAll();
		$this->template->urlAbsolutePath = $this->getURL()->hostUrl;
		$this->template->urlFullDomain = $this->getURL()->host;
		$this->template->defaultLocale = $this->defaultLocale;
	}

	public function handleChangeLocale(string $locale) {
		$this->redirect('this', ['locale' => $locale]);
	}

	public function createComponentContactForm()
	{
		$form = $this->contactFormFactory->createContactForm();
		$form->onSuccess[] = function($form) {
            $this->redirect('Default:default');
        };
		return $form;
	}

}