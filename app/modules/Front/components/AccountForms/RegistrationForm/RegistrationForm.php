<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\DateTime;
use \Contributte\Translation\Translator;
use App\Model\UserRepository;

class RegistrationForm extends Control {

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
        $this->template->setFile(__DIR__ . '/RegistrationForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {

        $form = new Form;

        $form->addEmail('email', $this->translate('m.account.email'))
            ->setRequired($this->translate('m.password.required'));

        $form->addPassword('new_password', $this->translate('m.account.newPassword'))
        ->setRequired($this->translate('m.formField.required'))
        ->addRule(Form::MIN_LENGTH, $this->translate('m.account.newPasswordMinLenght', ['passwordMinLenght' => 5]), 5);

        $form->addPassword('new_password_verify', $this->translate('m.account.newPasswordVerify'))
        ->setRequired($this->translate('m.formField.required'))
        ->addRule(Form::EQUAL, $this->translate('m.account.newPasswordMismatch'), $form['new_password']);

        $form->addSubmit('submit', $this->translate('m.account.registerNewUser'));

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Create new user
            $this->userRepository->registerNewUser($values);

            //Send email
            $this->sendRegistrationEmail($values);

            //Login user
            $user = $this->parent->getUser();
            $user->login($values['email'], $values['new_password'], 'FrontModule');

            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }

    private function sendRegistrationEmail($values)
    {

    }
}
