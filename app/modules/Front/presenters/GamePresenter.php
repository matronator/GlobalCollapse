<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;

/////////////////////// FRONT: Game PRESENTER ///////////////////////

abstract class GamePresenter extends BasePresenter
{
  protected function startup() {
    parent::startup();
    if (!$this->user->isLoggedIn() && ($this->getName() != "Login" || $this->getName() != "Signup")) {
      $this->canonicalize('Login:default');
    }
  }
}
