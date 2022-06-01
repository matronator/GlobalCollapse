<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use \Contributte\Translation\Translator;
use App\Libs\Mailer\Mailer;
use App\Model\UsersRepository;

class PasswordRecoveryForm extends Control {

    private $translator;
    private $mailer;
    private $usersRepository;
    public $onSuccess;
    public $onError;

    public function __construct(
        Translator $translator,
        Mailer $mailer,
        UsersRepository $usersRepository
    )
    {
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->usersRepository = $usersRepository;
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

        $form->addEmail('email', $this->translate('m.account.email'))
        ->setRequired($this->translate('m.formField.required'));

        $form->addSubmit('submitReset', $this->translate('m.account.changePassword'));
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Check if account with email exists
            $userAccount = $this->usersRepository->getUserByEmail($values['email'])->fetch();

            if(isset($userAccount)){
                //Create reset token and insert it to db
                $tokenValues = $this->usersRepository->createResetToken($values);

                //Send email
                $this->mailer->sendPasswordRecoveryEmail($tokenValues);
            }

            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}
