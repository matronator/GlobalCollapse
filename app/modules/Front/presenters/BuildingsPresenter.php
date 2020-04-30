<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;
use App\Model\UserRepository;
use App\Model\BuildingsRepository;

final class BuildingsPresenter extends GamePresenter
{
	private $userRepository;
  private $buildingsRepository;

	public function __construct(
		UserRepository $userRepository,
		BuildingsRepository $buildingsRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->buildingsRepository = $buildingsRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

  public function renderDefault() {

  }
}
