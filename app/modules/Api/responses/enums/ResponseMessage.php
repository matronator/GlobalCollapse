<?php

declare(strict_types=1);

namespace App\ApiModule\Responses\Enums;

enum ResponseMessage: string
{
    case NOT_PERMITTED = 'notPermitted';
    case NOT_LOGGED_IN = 'notLoggedIn';
}
