<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Services\StripeService;
use Tracy\Debugger;
use VoteCallback;

final class PremiumPresenter extends GamePresenter
{
    use VoteCallback;

    private StripeService $stripeService;

    public function __construct(
        StripeService $stripeService
    ) {
        parent::__construct();
        $this->stripeService = $stripeService;
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
