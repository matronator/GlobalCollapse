<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\UserRepository;
use ChangePasswordForm;
use ChangeEmailForm;
use DateTime;
use DatetimeForm;
use DateTimeZone;
use Timezones;

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
            $this->redirect('Login:default');
    }

    public function actionLogout()
    {
        $this->user->logout();
        $this->redirect('Default:default');
    }

    public function renderSettings()
    {
        $now = Timezones::getUserTime(new DateTime(), $this->userPrefs->timezone, $this->userPrefs->dst);
        $this->template->userLocalTime = $now;
    }

    public function createComponentChangePasswordForm()
    {
        $form = new ChangePasswordForm($this->userRepository);
        $form->onSuccess[] = function($form) {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $user = $this->getUser()->identity;
            $newPasswordHash = $this->userRepository->cypherPassword($values['newPassword']);
            $this->userRepository->changeUserPassword($user->id, $newPasswordHash);

            //Show flashmessage and redirect
            $this->flashMessage('Password changed', 'success');
        };
        $form->onError[] = function($form) {
            $this->flashMessage('Something went wrong', 'danger');
        };
        return $form;
    }

    public function createComponentChangeEmailForm()
    {
        $form = new ChangeEmailForm($this->userRepository);
        $form->onSuccess[] = function($form) {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $user = $this->getUser()->identity;
            $this->userRepository->changeUserEmail($user->id, $values['newEmail']);

            //Show flashmessage and redirect
            $this->flashMessage('E-mails changed', 'success');
        };
        $form->onError[] = function($form) {
            $this->flashMessage('Something went wrong', 'danger');
        };
        return $form;
    }

    public function createComponentDatetimeForm()
    {
        $form = new DatetimeForm($this->userRepository);
        $form->onSuccess[] = function($form) {
            //Get values as array
            $values = $form->getValues();
            $userSettings = $this->userRepository->getSettings($this->presenter->user->getIdentity()->id);
            $section = $this->session->getSection('preferences');
            if (!$values->dst) {
                $userSettings->update([
                    'timezone' => $values->timezone,
                    'dst' => 0
                ]);
                $section->dst = null;
            } else {
                $userSettings->update([
                    'timezone' => $values->timezone,
                    'dst' => 1
                ]);
                $section->dst = true;
            }
            $section->timezone = $values->timezone;
            $this->flashMessage('Datetime preferences updated', 'success');
        };
        $form->onError[] = function($form) {
            $this->flashMessage('Something went wrong', 'danger');
        };
        return $form;
    }
}
