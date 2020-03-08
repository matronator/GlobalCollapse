<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette,
	Nette\Application\UI\Form;


final class SignPresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @persistent */
	public $backlink = '';


	/*********************** COMPONENT FACTORIES ***********************/
	/**
	* Sign-in form factory.
	* @return Form
	*/
	protected function createComponentSignInForm()
	{
		$form = new Form;

		$form->addText('email', 'E-mail:')
		->setRequired('Vložte váš email.');

		$form->addPassword('password', 'Heslo:')
		->setRequired('Vložte vaše heslo.');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}


	public function signInFormSucceeded(Form $form, $values)
	{
		try {
			$this->getUser()->login($values->email, $values->password, NULL);

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			return;
		}

		$this->restoreRequest($this->backlink);
		$this->redirect('Default:');
	}

	/*********************** ACTIONS ***********************/
	
	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl/a jsi úspěšně odhlášen.');
		$this->redirect('in');
	}

}
