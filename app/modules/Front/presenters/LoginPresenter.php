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

	public function renderLogin()
	{
		$this->template->articles = $this->articles->findAll();
	}

	public function createComponentLoginForm(): Form
	{
			$form = new Form();
			$form->setHtmlAttribute('class', 'uk-form-horizontal');
			$form->addEmail('email', 'Email')
					->setHtmlAttribute('placeholder', 'E-mail')
					->setHtmlAttribute('class', 'uk-input')
					->setHtmlId('email')
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
					$this->getUser()->login($values->email, $values->password, NULL);
					if ($values->backlink) {
							$this->restoreRequest($values->backlink);
					} else {
							$this->redirect('Default:');
					}
			} catch (AuthenticationException $e) {
					$form->addError($e->getMessage());
			}
	}
}
