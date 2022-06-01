<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;
use ResetPasswordForm;

final class RecoverPresenter extends BasePresenter
{
    /** @var Model\UserRepository */
	private $userRepository;

	public function __construct(
		Model\UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

    protected function startup()
    {
        parent::startup();
    }

    public function renderDefault(string $hash) {
        $recovery = $this->userRepository->findAllUserPasswordReset()->where('hash', $hash)->fetch();
        if (!$hash || !$recovery) {
            $this->redirect('Default:default');
        }
    }

    public function createComponentResetPasswordForm()
    {
        $form = new ResetPasswordForm($this->userRepository);
        $form->onSuccess[] = function($form) {
            //Get values as array
            $values = $form->getValues(true);

            //Update password
            $hash = $this->getParameter('hash');
            $user = $this->userRepository->getUserByRecoveryHash($hash);
            $newPasswordHash = $this->userRepository->cypherPassword($values['newPassword']);
            $this->userRepository->changeUserPassword($user->id, $newPasswordHash);
            $this->userRepository->resetPassword($user->id);

            //Show flashmessage and redirect
            $this->flashMessage('Password reset', 'success');
            $this->redirect('Login:default');
        };
        $form->onError[] = function($form) {
            $this->flashMessage('Something went wrong', 'danger');
        };
        return $form;
    }
}
