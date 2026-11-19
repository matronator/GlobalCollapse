<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\CoinFlipGameResult;
use App\Model\GamblingRepository;
use App\Model\Result;
use Tracy\Debugger;

final class CasinoPresenter extends GamePresenter
{
    public function __construct(
        private GamblingRepository $gamblingRepository,
    ) {
        parent::__construct();
    }

    protected function startup()
    {
        parent::startup();
    }

    public function renderDefault()
    {
        $this->template->player = $this->player;
    }

    public function handleCoinFlip(int $betAmount, string $side)
    {
        if ($this->player->bitcoins < $betAmount) {
            $this->flashMessage('You do not have enough bitcoins.', 'danger');
            $this->redirect('this');
        }

        if (!in_array($side, ['heads', 'tails'])) {
            $this->flashMessage('Invalid side selected.', 'danger');
            $this->redirect('this');
        }

        try {
            $record = $this->gamblingRepository->playCoinFlip($this->player->id, $betAmount, $side === 'heads' ? CoinFlipGameResult::HEADS : CoinFlipGameResult::TAILS);
        } catch (\Exception $e) {
            if ($e instanceof \InvalidArgumentException) {
                $this->flashMessage($e->getMessage(), 'danger');
            } else {
                Debugger::log($e, Debugger::ERROR);
                $this->flashMessage('An error occurred while processing your bet.', 'danger');
            }
            $this->redirect('this');
        }

        if ($record->result === Result::WIN) {
            $this->userRepository->addBitcoins($this->player->id, $betAmount);
        } else {
            $this->userRepository->subtractBitcoins($this->player->id, $betAmount);
        }

        $this->template->flipResult = $record->game_result;
        $this->template->win = $record->result === Result::WIN;
        $this->template->resultBetAmount = $betAmount;

        if ($this->isAjax()) {
            $this->redrawControl('gambleArea');
            $this->redrawControl('sidebarStats');
        } else {
            // For non-ajax, we'll just let the render handle it,
            // but we need to pass these values to the template.
            // In Nette handle methods, we can use flash messages or persist in session if needed,
            // but since we want to show the animation, we'll use template variables and redirect carefully or just render.
        }
    }
}
