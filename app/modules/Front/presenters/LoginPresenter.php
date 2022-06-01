<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\UserRepository;
use Latte\Engine;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

/////////////////////// FRONT: Login PRESENTER ///////////////////////

final class LoginPresenter extends BasePresenter
{

	private $userRepository;

	public function __construct(
		UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
		parent::startup();
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			if ($player->tutorial === 0) {
				$this->redirect('Intro:default');
			} else {
				$this->redirect('Default:default');
			}
		}
	}

	public function renderLogin()
	{
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
			$form->addError($this->translate('general.messages.danger.wrongCredentials'), false);
		}
	}

	public function createComponentRestoreForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'uk-form-horizontal');
		$form->addEmail('email', 'E-mail')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlId('email')
				->setHtmlAttribute('placeholder', 'your@email.com')
				->setRequired();
		$form->addCheckbox('username', ' Forgot username')
				->setHtmlAttribute('class', 'uk-checkbox')
				->setHtmlId('username');
		$form->addCheckbox('password', ' Forgot password')
				->setHtmlAttribute('class', 'uk-checkbox')
				->setHtmlId('password');
		$form->addHidden('backlink', $this->getParameter('backlink'));
		$form->addSubmit('submit', 'Login');
		$form->onSuccess[] = [$this, 'restoreFormSucceeded'];
		return $form;
	}

	public function restoreFormSucceeded(Form $form, ArrayHash $values): void
	{
		$user = $this->userRepository->getUserByEmail($values->email);
		if ($user) {
			$latte = new Engine;
			$hash = '';
			if ($values->password) {
				$hash = $this->userRepository->generateResetPasswordHash($user->id);
			}
			$params = [
				'forgotName' => $values->username,
				'forgotPass' => $values->password,
				'username' => $user->username,
				'hash' => $hash,
				'link' => $values->password ? $this->link('//Recover:default', ['hash' => $hash]) : '',
			];
			$mail = new Message;
			$mail->setFrom('no-reply@global-collapse.com', 'Global Collapse Account Recovery')
				->addTo($values->email)
				->setSubject('Account Recovery')
				->setHtmlBody($latte->renderToString(__DIR__ . '/../templates/_recoverMail.latte', $params));

			$mailer = new SendmailMailer;
			$mailer->send($mail);

			$this->flashMessage('E-mail with additional info was sent to the address you provided. Please check it to recover your account.', 'success');
			$this->redirect('Login:done');
		} else {
			$form->addError($this->translate('general.messages.danger.forgotPasswordNoUser'));
			$this->flashMessage('User with this e-mail address was not found.', 'danger');
		}
	}
}
