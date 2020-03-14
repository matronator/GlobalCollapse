<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;


/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class DefaultPresenter extends BasePresenter
{
	/** @var Model\ArticlesRepository */
	private $articles;

	private UserRepository $userRepository;

	public function __construct(
		UserRepository $userRepository,
		Model\ArticlesRepository $articles
	)
	{
		$this->articles = $articles;
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDefault()
	{
		$this->template->articles = $this->articles->findAll();
	}
}
