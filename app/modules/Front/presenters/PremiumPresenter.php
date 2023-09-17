<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Services\StripeService;
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
        $this->redirect('Default:default');
    }

    public function renderDefault()
    {
        $this->template->stripePublicKey = $this->stripeService->publicKey;
    }

    public function renderManage()
    {
        if ($this->player->tier <= 1) {
            $this->redirect('default');
        }
        $this->template->stripePublicKey = $this->stripeService->publicKey;
    }

    public function handleUpgradeTier(int $tier = 1)
    {
    }
}
