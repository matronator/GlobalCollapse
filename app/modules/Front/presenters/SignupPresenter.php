<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\OrderRepository;
use App\Model\UserRepository;
use App\Services\GoPayService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Security\Passwords;

final class SignupPresenter extends BasePresenter
{
    private $userRepository;

    private $passwords;

    public function __construct(
        UserRepository $userRepository,
        Passwords $passwords
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->passwords = $passwords;
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

    public function renderDefault(): void
    {
        $this->template->anything = 'anything';
    }

    public function createComponentSignupForm(): Form
    {
        $form = new Form();
        $form->setHtmlAttribute('class', 'uk-form-horizontal');
        $form->addEmail('email', 'Email')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('email')
            ->setHtmlAttribute('placeholder', 'email@example.com')
            ->setRequired();
        $form->addText('username', 'Username')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('username')
            ->setHtmlAttribute('placeholder', 'username')
            ->addRule(Form::MIN_LENGTH, 'Username must be at least 3 characters long', 3)
            ->setRequired();
        $form->addPassword('password', 'Password')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('password')
            ->setHtmlAttribute('placeholder', 'Password')
            ->addRule(Form::MIN_LENGTH, 'Password has to be at least 6 characters long', 6)
            ->setRequired();
        $form->addPassword('passwordAgain', 'Repeat password')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('passwordAgain')
            ->setHtmlAttribute('placeholder', 'Repeat password')
            ->setRequired();
        $form->addSubmit('save', 'Sign Up');
        $form->onSuccess[] = [$this, 'signupFormSucceeded'];
        return $form;
	}

    public function signupFormSucceeded(Form $form, ArrayHash $values): void
    {
        try {
            if ($values->password !== $values->passwordAgain) {
                throw new BadRequestException('Passwords don\'t match');
            }
            $values->password = $this->passwords->hash($values->password);
            unset($values->passwordAgain);
            $this->userRepository->createUser($values);
            $this->flashMessage($this->translate('general.messages.success.registration'), 'success');
            $this->redirect('Login:default');
        } catch (BadRequestException $e) {
            $this->flashMessage($e->getMessage(), 'warning');
            $form->addError($e->getMessage());
            $this->redirect('this');
        }
	}
}
