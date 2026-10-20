<?php

declare(strict_types=1);

namespace App\ApiModule\Responses;

use App\ApiModule\Responses\Enums\ResponseMessage;
use App\ApiModule\Responses\Enums\ResponseStatus;
use Contributte\Translation\Translator;

class ResponseFactory
{
    public function __construct(public Translator $translator)
    {
    }

    public function create(ResponseStatus $status, ResponseMessage $message, ?array $data = null): array
    {
        $return = [
            'status' => $status->value,
            'message' => $this->translator->translate('api.response.' . $message->value),
        ];

        if ($data) {
            $return['data'] = $data;
        }

        return $return;
    }
}
