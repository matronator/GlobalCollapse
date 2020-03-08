<?php

namespace App\Components;

use App\Model\ActionRepository;
use App\Model\CategoryRepository;
use App\Model\HotelRepository;
use App\Model\PartnerRepository;
use Nette\Application\UI\Form;

class FilterForm extends \Nette\Application\UI\Control
{
    private $locale;
    private $partnerId;
    private $hotelId;
    private $fields;

    public $onFilter;

    /** @var CategoryRepository */
    private $categoryModel;

    /** @var HotelRepository */
    private $hotelModel;

    /** @var PartnerRepository */
    private $partnerModel;

    /** @var ActionRepository */
    private $actionModel;

    public function __construct(
        $fields,
        $locale,
        $partnerId,
        $hotelId,
        CategoryRepository $categoryModel,
        HotelRepository $hotelModel,
        PartnerRepository $partnerModel,
        ActionRepository $actionModel
    )
    {
        parent::__construct();
        $this->fields = $fields;
        $this->locale = $locale;
        $this->partnerId = $partnerId;
        $this->hotelId = $hotelId;
        $this->categoryModel = $categoryModel;
        $this->hotelModel = $hotelModel;
        $this->partnerModel = $partnerModel;
        $this->actionModel = $actionModel;
    }

    public function render()
    {
        $this->template->fields = $this->fields;
        $this->template->render(__DIR__ . '/FilterForm.latte');
    }

    protected function createComponentFilterForm()
    {
        $partners = $this->partnerModel->getPairs();
        $hotels = $this->hotelModel->getPairs();

        $form = new Form;

        $form->setMethod('GET');

        if (in_array('partnerId', $this->fields))
            $form->addSelect('partnerId', 'Partner')
                ->setItems($this->partnerId ? [] : $partners)
                ->setPrompt('Vyberte partnera');

        if (in_array('hotelId', $this->fields))
            $form->addSelect('hotelId', 'Hotel')
                ->setItems($this->hotelId ? [] : $hotels)
                ->setPrompt('Vyberte hotel');

        if (in_array('categoryId', $this->fields))
            $form->addSelect('categoryId', 'Kategorie')
                ->setItems($this->categoryModel->getPairs($this->locale))
                ->setPrompt('Vyberte kategorii');

        if (in_array('actionId', $this->fields))
            $form->addSelect('actionId', 'Akce')
                ->setItems($this->partnerId || $this->hotelId ? [] : $this->actionModel->getPairs($this->locale))
                ->setPrompt('Vyberte akci');

        if (in_array('state', $this->fields))
            $form->addSelect('state', 'Stav')
                ->setItems([0 => 'Vyberte stav', 'Zaplaceno', 'Použito']);

        if (in_array('orderType', $this->fields))
            $form->addSelect('orderType', 'Typ')
                ->setItems([1 => 'Akce', 2 => 'Služba'])
                ->setPrompt('Vyberte typ');

        if (in_array('dateFrom', $this->fields))
            $form->addText('dateFrom', 'Od')
                ->setAttribute('placeholder', 'Od');

        if (in_array('dateTo', $this->fields))
            $form->addText('dateTo', 'Do')
                ->setAttribute('placeholder', 'Do');

        if (in_array('query', $this->fields))
            $form->addText('query')
                ->setAttribute('placeholder', 'Hledaný výraz');


        $form->addSubmit('send', 'Filtrovat');

        $form->onSuccess[] = [$this, 'filterFormSucceeded'];

        return $form;
    }

    public function filterFormSucceeded($form, $values)
    {
        $this->onFilter($this, $values);
    }
}