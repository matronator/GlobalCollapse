<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\CoinFlipGameResult;
use App\Model\GamblingRepository;
use App\Model\Result;
use App\Services\StripeService;
use Tracy\Debugger;
use VoteCallback;

final class PremiumPresenter extends GamePresenter
{
    use VoteCallback;

    public function __construct(
        private StripeService $stripeService,
        private GamblingRepository $gamblingRepository,
    ) {
        parent::__construct();
    }

    protected function startup()
    {
        parent::startup();
        // $this->redirect('Default:default');
    }

    public function renderDefault()
    {
        $this->template->stripePublicKey = $this->stripeService->publicKey;
        $this->template->dev = $this->stripeService->devMode;
    }

    public function renderGamble()
    {
        $this->template->player = $this->player;
    }

    public function renderManage()
    {
        if ($this->player->tier <= 1) {
            $this->redirect('default');
        }
        $this->template->stripePublicKey = $this->stripeService->publicKey;
    }

    public function renderSuccess(string $sessionId = null, ?string $mode = null)
    {
        if (!$sessionId) {
            $this->redirect('default');
        }

        $this->template->mode = $mode;
    }

    public function renderCancel(?string $mode = null)
    {
        $this->template->mode = $mode;
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
            $this->redrawControl('sidebar-stats');
        } else {
            // For non-ajax, we'll just let the render handle it,
            // but we need to pass these values to the template.
            // In Nette handle methods, we can use flash messages or persist in session if needed,
            // but since we want to show the animation, we'll use template variables and redirect carefully or just render.
        }
    }

    public function handleUpgradeAccount(string $item)
    {
        $cs = $this->stripeService->createCheckoutSession($item, $this->player);
        $this->redirectUrl($cs->url);
    }

    public function handleBuyBitcoins(string $item)
    {
        $cs = $this->stripeService->createCheckoutSession($item, $this->player, 'payment');
        $this->redirectUrl($cs->url);
    }
}
