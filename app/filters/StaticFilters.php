<?php

namespace App\Filter;

use Nette\Utils\Strings;

class StaticFilters
{
    public static function common($filter, $value)
    {
        if (method_exists(__CLASS__, $filter)) {
            $args = func_get_args();
            array_shift($args);
            return call_user_func_array(array(__CLASS__, $filter), $args);
        }
        return null;
    }

    public static function join($arr)
    {
        $filter = array_filter(
            $arr,
            function ($i) {
                return $i === '' ? false : true;
            }
        );
        return join(' ', $filter);
    }

    public static function nbsp($text)
    {
        return preg_replace('/(\s)([a-zA-z])\s/i', '$1$2&nbsp;', $text);
    }

    public static function fromSnake(string $text): string
    {
        return Strings::trim(Strings::capitalize(preg_replace('/([a-z]+)_?([a-z])*?/', '$1 $2', $text)));
    }

    public static function firstLower(string $text): string
    {
        return Strings::firstLower($text);
    }

    public static function time(string $text, bool $longFormat = false): string
    {
        $time = (int) $text;
        $hours = floor($time / 60);
        $minutes = $time % 60;

        return $longFormat ? sprintf('%d hours and %d minutes', $hours, $minutes) : sprintf('%02d:%02d', $hours, $minutes);
    }
}
