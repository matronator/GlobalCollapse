<?php

declare(strict_types=1);

class Math
{
    public static function random(float $min, float $max, float $gamma = 1): int
    {
        $offset = $max - $min + 1;
        return (int) floor($min + pow(lcg_value(), $gamma) * $offset);
    }

    public static function randomDist(float $mean, float $sd): float
    {
        $x = rand() / getrandmax();
        $y = rand() / getrandmax();
        return sqrt(-2 * log($x)) * cos(2 * pi() * $y) * $sd + $mean;
    }

    public static function getRarity(): string
    {
        $number = self::random(1, 100);
        switch (true) {
            case $number <= 50:
                return 'common';
            case $number <= 80:
                return 'rare';
            case $number <= 95:
                return 'epic';
            case $number <= 100:
                return 'legendary';
            default:
                return 'common';
        }
    }
}
