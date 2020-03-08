<?php

declare(strict_types=1);

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use \Kdyby\Translation\Translator;
use App\Model\UserRepository;

class LogInForm extends Control {

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
        $this->template->setFile(__DIR__ . '/LogInForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {

        $form = new Form;

        $form->addEmail('email', $this->translator->translate('m.account.email'))
            ->setRequired($this->translator->translate('m.password.required'));

        $form->addPassword('password', $this->translator->translate('m.account.password'))
            ->setRequired($this->translator->translate('m.password.required'));

        $form->addSubmit('submit', $this->translator->translate('m.account.logIn'));

        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    public function processForm(Form $form)
    {
        try {
            //Get values as array
            $values = $form->getValues(true);

            //Login
            $user = $this->parent->getUser();
            $user->login($values['email'], $values['password'], 'FrontModule');

            //Then set user info data
            $userIdentity = $this->parent->getUser()->identity;
            $this->userRepository->setUserInfo($userIdentity->data);

            $this->onSuccess($form);
        } catch (Nette\Security\AuthenticationException $e) {
            $this->onError($form);
        }
    }
}