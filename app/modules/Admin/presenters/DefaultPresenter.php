<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;


/////////////////////// ADMIN: DEFAULT PRESENTER ///////////////////////

final class DefaultPresenter extends BasePresenter
{

	protected function startup()
	{
		parent::startup();
	}

	/*********************** RENDER VIEWS ***********************/
	
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

}
