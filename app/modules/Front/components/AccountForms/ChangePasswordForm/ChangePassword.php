<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use \Kdyby\Translation\Translator;
use App\Model\UserRepository;

class ChangePasswordForm extends Control {

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
        $this->template->setParameters($params);
        $this->template->setFile(__DIR__ . '/ChangePasswordForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $form = new Form;

        $form->addPassword('new_password', $this->translator->translate('m.account.newPassword'))
        ->setRequired($this->translator->translate('m.formField.required'))
        ->addRule(Form::MIN_LENGTH, $this->translator->translate('m.account.newPasswordMinLenght', ['passwordMinLenght' => 5]), 5);

        $form->addPassword('new_password_verify', $this->translator->translate('m.account.newPasswordVerify'))
        ->setRequired($this->translator->translate('m.formField.required'))
        ->addRule(Form::EQUAL, $this->translator->translate('m.account.newPasswordMismatch'), $form['new_password']);

        $form->addSubmit('submit', $this->translator->translate('m.account.changePassword'));
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $user = $this->parent->getUser()->identity;
            $newPasswordHash = $this->userRepository->cypherPassword($values['new_password']);
            $this->userRepository->changeUserPassword($user->id, $newPasswordHash);

            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}