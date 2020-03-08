<?php

declare(strict_types=1);

namespace App\BaseModule\Presenters;

use Nette;


class Error4xxPresenter extends BasePresenter
{

	public function startup()
	{
		parent::startup();
		if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}


	public function renderDefault()
	{
        $path = __DIR__ . "/templates/Error/404.latte";
        $this->template->setFile($path);
	}

}
