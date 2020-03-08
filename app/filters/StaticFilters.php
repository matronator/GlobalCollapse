<?php

namespace App\Filter;

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

}