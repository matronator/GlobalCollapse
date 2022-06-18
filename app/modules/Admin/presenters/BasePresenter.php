<?php



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

        if (!$this->isLinkCurrent('Vendor:updateOffers') || !$this->isLinkCurrent('Vendor:updatePrices')) {
            if (!$this->user->isLoggedIn())
                $this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);

            if (!$this->isAllowed('read')) {
                $this->redirect('Sign:in');
                // throw new ForbiddenRequestException();
            }
        }
	}

    public function isAllowed($privilege, $resource = null)
    {
        $resource = $resource ? $resource : explode(':', $this->name)[1]; // current presenter name as fallback
        return $this->user->isAllowed($resource, $privilege);
    }

    public function checkPrivilege($privilege)
    {
        if (!$this->isAllowed($privilege))
        {
            // throw new ForbiddenRequestException('Na akci nemáte práva');
            $this->user->logout();
            $this->redirect('Sign:in');
        }
    }

    public function beforeRender()
    {
        $this->template->user = (object) $this->user->getIdentity()->data;
        $this->template->navItems = [
            (object) [
                'presenter' => 'Article',
                'title' => 'Articles',
                'icon' => ' file-text'
            ],
            (object) [
                'presenter' => 'User',
                'title' => 'Users',
                'icon' => 'users'
            ],
            (object) [
                'presenter' => 'Vendor',
                'title' => 'Vendors',
                'icon' => 'cart'
            ],
            (object) [
                'presenter' => 'Offers',
                'title' => 'Offers',
                'icon' => 'cart'
            ]
        ];
    }

    public function handleLogout()
    {
        $this->user->logout();
        $this->redirect('Sign:in');
    }
}
