<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use Timezones;

/////////////////////// FRONT: Game PRESENTER ///////////////////////

abstract class GamePresenter extends BasePresenter
{
  protected function startup() {
    parent::startup();
    if (!$this->user->isLoggedIn() && (!$this->presenter->isLinkCurrent('Login:*') || !$this->presenter->isLinkCurrent('Signup:*'))) {
      $this->redirect('Login:default');
    }
  }

	public function toUserDate($date) {
    return Timezones::getUserTime($date, $this->userPrefs->timezone, $this->userPrefs->dst);
	}
}
