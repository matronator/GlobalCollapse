<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use \Kdyby\Translation\Translator;
use App\Model\UserRepository;

class PasswordRecoveryForm extends Control {

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
        $this->template->setFile(__DIR__ . '/PasswordRecoveryForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $form = new Form;

        $form->addEmail('email', $this->translator->translate('m.account.email'))
        ->setRequired($this->translator->translate('m.formField.required'));

        $form->addSubmit('submit', $this->translator->translate('m.account.changePassword'));
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Check if account with email exists
            $userAccount = $this->userRepository->getUserByEmail($values['email'])->fetch();

            if(isset($userAccount)){
                //Create reset token and insert it to db
                $tokenValues = $this->userRepository->createResetToken($values);

                //Send email
                $this->sendPasswordRecoveryEmail($tokenValues);
            }

            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }

    private function sendPasswordRecoveryEmail($tokenValues)
    {

    }
}