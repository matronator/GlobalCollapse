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
        $form->addEmail('email', 'Email')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('email')
            ->setHtmlAttribute('placeholder', 'Email')
            ->addRule(Form::EMAIL, 'Email not valid')
            ->setRequired();
        $form->addEmail('newEmail', 'New email')
            ->setHtmlAttribute('class', 'uk-input')
            ->setHtmlId('newEmail')
            ->setHtmlAttribute('placeholder', 'New email')
            ->addRule(Form::EMAIL, 'Email not valid')
            ->addRule(FORM::EQUAL, 'Emails don\'t match', $form['email'])
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
            $this->userRepository->changeUserEmail($user->id, $values['newEmail']);
            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}
