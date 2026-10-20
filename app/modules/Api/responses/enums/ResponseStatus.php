<?php

declare(strict_types=1);

namespace App\ApiModule\Responses\Enums;

enum ResponseStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
}
