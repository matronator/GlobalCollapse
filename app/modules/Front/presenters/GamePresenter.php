<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use Timezones;

/////////////////////// FRONT: Game PRESENTER ///////////////////////

abstract class GamePresenter extends BasePresenter
{
  public $_player;

  protected function startup() {
    parent::startup();
    if (!$this->user->isLoggedIn() && (!$this->presenter->isLinkCurrent('Login:*') || !$this->presenter->isLinkCurrent('Signup:*'))) {
      $this->redirect('Login:default');
    }

    $this->_player = $this->userRepository->getUser($this->user->getIdentity()->id);
  }

	public function toUserDate($date) {
    return Timezones::getUserTime($date, $this->userPrefs->timezone, $this->userPrefs->dst);
	}
}
