<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;


/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class DefaultPresenter extends BasePresenter
{
	/** @var Model\ArticlesRepository */
	private $articles;

	public function __construct(
		Model\ArticlesRepository $articles
	)
	{
		$this->articles = $articles;
	}

	public function renderDefault()
	{
		$this->template->articles = $this->articles->findAll();
	}
}
