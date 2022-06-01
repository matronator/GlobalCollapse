<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use App\Model\UserRepository;
use Nette\Application\BadRequestException;

class ResetPasswordForm extends Control {

    private $userRepository;
    public $onSuccess;
    public $onError;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function render($params = [])
    {
        $this->template->setParameters($params);
        $this->template->setFile(__DIR__ . '/ResetPasswordForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $hash = $this->presenter->getParameter('hash');
        $user = $this->userRepository->getUserByRecoveryHash($hash);

        $form = new Form;
        $form->addPassword('newPassword', 'New password')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('newPassword')
            ->setHtmlAttribute('placeholder', 'New password')
            ->addRule(Form::MIN_LENGTH, 'Password has to be at least 6 characters long', 6)
            ->setRequired();
        $form->addPassword('passwordAgain', 'Repeat password')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('passwordAgain')
            ->setHtmlAttribute('placeholder', 'Repeat password')
            ->addRule(Form::EQUAL, 'Passwords don\'t match', $form['newPassword'])
            ->setRequired();
        $form->addHidden('username', $user->username);
        $form->addSubmit('submit', 'Reset password');

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $hash = $this->presenter->getParameter('hash');
            $user = $this->userRepository->getUserByRecoveryHash($hash);
            $newPasswordHash = $this->userRepository->cypherPassword($values['newPassword']);
            $this->userRepository->changeUserPassword($user->id, $newPasswordHash);
            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}
