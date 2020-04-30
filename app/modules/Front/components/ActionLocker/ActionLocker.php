<?php

declare(strict_types=1);

use Nette\Application\UI\Component;

class ActionLocker extends Component
{
  public function checkActions($player, $presenter)
  {
    $actions = [];
    $actions['scavenging'] = $player->actions->scavenging;
    $actions['training'] = $player->actions->training;
    $actions['mission'] = $player->actions->on_mission;
    $actions['resting'] = $player->actions->resting;

    if ($actions['scavenging'] == 1 && ($presenter->isLinkCurrent('City:darknet') || $presenter->isLinkCurrent('Bar:default'))) {
      $presenter->redirect('City:wastelands');
    }
    if ($actions['resting'] == 1 && $presenter->isLinkCurrent('Bar:default')) {
      $presenter->redirect('Default:rest');
    }
    if ($actions['mission'] == 1 && $presenter->isLinkCurrent('Default:rest')) {
      $presenter->redirect('Bar:default');
    } else if ($actions['mission'] == 1 && $presenter->isLinkCurrent('City:wastelands')) {
      $presenter->redirect('Bar:default');
    }
  }
}
