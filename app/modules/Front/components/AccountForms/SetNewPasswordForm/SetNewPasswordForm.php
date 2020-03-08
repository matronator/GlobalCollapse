<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use \Kdyby\Translation\Translator;
use App\Model\UserRepository;

class SetNewPasswordForm extends Control {

    private $translator;
    private $userRepository;
    public $onSuccess;
    public $onError;

    public function __construct(
        Translator $translator,
        UserRepository $userRepository
    )
    {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
    }

    public function render($params = [])
    {
        $this->template->links = (object) [
            'recovery' => $this->parent->link('Account:passwordRecovery'),
            'registration' => $this->parent->link('Account:registration')
        ];
        $this->template->setParameters($params);
        $this->template->setFile(__DIR__ . '/SetNewPasswordForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {

        $form = new Form;

        $form->addEmail('email', $this->translator->translate('m.account.email'))
            ->setRequired($this->translator->translate('m.password.required'));

        $form->addPassword('new_password', $this->translator->translate('m.account.newPassword'))
        ->setRequired($this->translator->translate('m.formField.required'))
        ->addRule(Form::MIN_LENGTH, $this->translator->translate('m.account.newPasswordMinLenght', ['passwordMinLenght' => 5]), 5);

        $form->addPassword('new_password_verify', $this->translator->translate('m.account.newPasswordVerify'))
        ->setRequired($this->translator->translate('m.formField.required'))
        ->addRule(Form::EQUAL, $this->translator->translate('m.account.newPasswordMismatch'), $form['new_password']);

        $form->addSubmit('submit', $this->translator->translate('m.account.setNewPassword'));

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Set new password for user
            $this->userRepository->setNewPassword($values);

            //Login user
            $user = $this->parent->getUser();
            $user->login($values['email'], $values['new_password'], 'FrontModule');

            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}