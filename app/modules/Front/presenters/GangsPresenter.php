<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\GangsRepository;

final class GangsPresenter extends GamePresenter
{
    private GangsRepository $gangsRepository;

    public function __construct(GangsRepository $gangsRepository)
    {
        parent::__construct();
        $this->gangsRepository = $gangsRepository;
    }

    public function renderDefault()
    {
    }
}
