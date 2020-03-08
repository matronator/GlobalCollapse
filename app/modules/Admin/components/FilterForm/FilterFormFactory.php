<?php

namespace App\Factories;

use App\Components\FilterForm;
use App\Model\ActionRepository;
use App\Model\CategoryRepository;
use App\Model\HotelRepository;
use App\Model\PartnerRepository;

class FilterFormFactory
{
    /** @var HotelRepository */
    private $hotelModel;

    /** @var PartnerRepository */
    private $partnerModel;

    /** @var CategoryRepository */
    private $categoryModel;

    /** @var ActionRepository */
    private $actionModel;

    public function __construct(
        CategoryRepository $categoryModel,
        HotelRepository $hotelModel,
        PartnerRepository $partnerModel,
        ActionRepository $actionModel
    )
    {
        $this->categoryModel = $categoryModel;
        $this->hotelModel = $hotelModel;
        $this->partnerModel = $partnerModel;
        $this->actionModel = $actionModel;
    }

    public function create($fields, $locale, $partnerId, $hotelId)
    {
        return new FilterForm(
            $fields,
            $locale,
            $partnerId,
            $hotelId,
            $this->categoryModel,
            $this->hotelModel,
            $this->partnerModel,
            $this->actionModel
        );
    }
}