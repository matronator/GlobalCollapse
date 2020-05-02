<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\UserRepository;
use ChangePasswordForm;

final class AccountPresenter extends BasePresenter
{
    private $userRepository;
    private $userData = null;

    public function __construct(
	    UserRepository $userRepository
	)
	{
    parent::__construct();
    $this->userRepository = $userRepository;
  }

    protected function startup()
    {
        parent::startup();

        if (!$this->user->isLoggedIn())
            $this->canonicalize('Login:default');
    }

    public function actionLogout()
    {
        $this->user->logout();
        $this->canonicalize('Default:');
    }

    public function renderSettings()
    {
    }

    public function createComponentChangePasswordForm()
    {
        $form = $this->createChangePasswordForm();
        $form->onSuccess[] = function($form) {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $user = $this->getUser()->identity;
            $newPasswordHash = $this->userRepository->cypherPassword($values['newPassword']);
            $this->userRepository->changeUserPassword($user->id, $newPasswordHash);

            //Show flashmessage and redirect
            $this->flashMessage('Password changed', 'success');
            $this->canonicalize('this');
        };
        $form->onError[] = function($form) {
            $this->flashMessage('Something went wrong', 'danger');
        };
        return $form;
    }

    public function createChangePasswordForm()
	{
		return new ChangePasswordForm($this->userRepository);
	}
}
