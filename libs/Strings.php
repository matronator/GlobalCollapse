<?php

declare(strict_types=1);

class Strings
{
    public static function capitalize(string $text)
    {
        $str = ucwords($text);
        $exclude = 'a,an,the,for,and,nor,but,or,yet,so,such,as,at,around,by,after,along,for,from,of,on,to,with,without';
        $excluded = explode(",", $exclude);
        foreach($excluded as $noCap) {
            $str = str_replace(ucwords($noCap), strtolower($noCap), $str);
        }
        return ucfirst($str);
    }

    public static function is_json(?string $str = null)
    {
        $json = json_decode($str);
        return $str === '' || $str === null || ($json && $str != $json);
    }
}
