<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Services\MailService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;

/////////////////////// FRONT: Login PRESENTER ///////////////////////

final class LoginPresenter extends BasePresenter
{
	/** @var Model\ArticlesRepository */
	private $articles;

	private $userRepository;

	public function __construct(
		UserRepository $userRepository,
		Model\ArticlesRepository $articles
	)
	{
		$this->articles = $articles;
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
		parent::startup();
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			if ($player->tutorial == 0) {
				$this->redirect('Intro:default');
			} else {
				$this->redirect('Default:default');
			}
		}
	}

	public function renderLogin()
	{
		$this->template->articles = $this->articles->findAll();
	}

	public function createComponentLoginForm(): Form
	{
			$form = new Form();
			$form->setHtmlAttribute('class', 'uk-form-horizontal');
			$form->addText('username', 'Username')
					->setHtmlAttribute('class', 'uk-input')
					->setHtmlId('username')
					->setHtmlAttribute('placeholder', 'Username')
					->setRequired();
			$form->addPassword('password', 'Password')
					->setHtmlAttribute('placeholder', 'Password')
					->setHtmlAttribute('class', 'uk-input')
					->setHtmlId('password')
					->setRequired();
			$form->addHidden('backlink', $this->getParameter('backlink'));
			$form->addSubmit('submit', 'Login');
			$form->onSuccess[] = [$this, 'loginFormSucceeded'];
			return $form;
	}

	public function loginFormSucceeded(Form $form, ArrayHash $values): void
	{
			try {
					$this->getUser()->login($values->username, $values->password);
					$player = $this->userRepository->getUser($this->user->getIdentity()->id);
					if ($player) {
						if ($player->tutorial === 0) {
							$this->redirect('Intro:default');
						} else {
							$this->redirect('Default:default');
						}
					}
			} catch (AuthenticationException $e) {
					$form->addError($e->getMessage());
			}
	}
}
