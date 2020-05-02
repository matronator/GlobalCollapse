<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\UserRepository;

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
        $this->log($this->user->getIdentity()->username, 'logout');
        $this->user->logout();
        $this->redirect('Default:');
    }

    public function renderSettings()
    {

    }
}
