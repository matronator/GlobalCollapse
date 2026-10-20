<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\ApiModule\Responses\Enums\ResponseMessage;
use App\ApiModule\Responses\Enums\ResponseStatus;

final class AccountPresenter extends BasePresenter
{
    public function actionDefault(): void
    {
        $this->sendApiResponse($this->responseFactory->create(
            ResponseStatus::SUCCESS,
            ResponseMessage::NOT_LOGGED_IN,
            [
                'user' => $this->user->getIdentity()->id,
                'email' => $this->user->getIdentity()->email,
                'username' => $this->user->getIdentity()->username,
                'created_at' => $this->user->getIdentity()->created_at,
            ]
        ));
    }
}
