<?php

declare(strict_types=1);

class CustomValidators
{
    public static function validateJson(\Nette\Forms\Controls\BaseControl $input, $arg): bool
    {
        return Strings::is_json($input->getValue());
    }
}
