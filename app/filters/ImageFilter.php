<?php

namespace App\Filter;

use ImageGenerator;

class ImageFilter
{
    /**
     * @var ImageGenerator
     */
    private $imageGenerator;

    public function __construct(
        ImageGenerator $imageGenerator
    )
    {
        $this->imageGenerator = $imageGenerator;
    }

    public function __invoke(
        $s,
        $crop = false,
        $w = 200,
        $h = 200,
        $cropType = 'center'
    )
    {
        $imageGenerator = $this->imageGenerator;
        $imageGenerator->setUrl($s);
        $imageGenerator->setCropImage($crop);
        $imageGenerator->setWidth($w);
        $imageGenerator->setHeight($h);
        $imageGenerator->setCropType($cropType);
        return $imageGenerator->getUrlThumb();
    }
}