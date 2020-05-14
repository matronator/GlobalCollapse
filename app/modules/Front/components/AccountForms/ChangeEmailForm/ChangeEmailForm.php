<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use App\Model\UserRepository;
use Nette\Application\BadRequestException;

class ChangeEmailForm extends Control {

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
        $this->template->setFile(__DIR__ . '/ChangeEmailForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $form = new Form;
        $form->addPassword('password', 'Password')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('password')
            ->setHtmlAttribute('placeholder', 'Password')
            ->setRequired();
        $form->addEmail('newEmail', 'New email')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('newEmail')
            ->setHtmlAttribute('placeholder', 'New email')
            ->addRule(Form::EMAIL, 'Email not valid')
            ->setRequired();
        $form->addSubmit('submit', 'Change e-mail');

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $user = $this->presenter->user->getIdentity();
            $newPasswordHash = $this->userRepository->cypherPassword($values['newPassword']);
            $this->userRepository->changeUserPassword($user->id, $newPasswordHash);
            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}
