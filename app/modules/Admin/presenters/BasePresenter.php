<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\UserRepository;
use Nette\Application\ForbiddenRequestException;

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
    /** @var UserRepository */
    private $userModel;

    public function injectRepository(UserRepository $userModel)
    {
        $this->userModel = $userModel;
    }

	protected function startup()
	{
		parent::startup();

        if (!$this->user->isLoggedIn())
            $this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);

        if (!$this->isAllowed('read'))
            throw new ForbiddenRequestException();
	}

    public function isAllowed($privilege, $resource = null)
    {
        $resource = $resource ? $resource : explode(':', $this->name)[1]; // current presenter name as fallback
        return $this->user->isAllowed($resource, $privilege);
    }

    public function beforeRender()
    {
        $this->template->user = (object) $this->user->getIdentity()->data;
        $this->template->navItems = $this->userModel->getNavItems();
    }

    public function handleLogout()
    {
        $this->user->logout();
        $this->redirect('Sign:in');
    }
}
