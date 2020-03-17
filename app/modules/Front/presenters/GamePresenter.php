<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

abstract class GamePresenter extends BasePresenter
{
  public $player;

  protected function startup() {
    parent::startup();
    if (!$this->user->isLoggedIn() && ($this->getName() != "Login" || $this->getName() != "Signup")) {
      $this->redirect('Login:default', ['backlink' => $this->storeRequest()]);
    } else {
      $this->player = $this->user->getIdentity();
    }
  }
}
