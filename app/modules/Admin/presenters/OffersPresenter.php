<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use DateTime;
use Nette,
	Nette\Application\UI\Form,
	App\Model;

final class OffersPresenter extends BasePresenter
{
	/** @var Model\DrugsRepository */
	private $darknet;

	public function __construct(Model\DrugsRepository $darknet)
	{
		$this->darknet = $darknet;
	}

	protected function startup()
	{
		parent::startup();
	}

	/*********************** RENDER VIEWS ***********************/

	public function renderDefault()
	{
		$this->template->data = $this->darknet->findAllOffers()->order('vendor.level');
	}

	public function renderEdit(int $id = null, int $vendorId = null)
	{
		$form = $this['offerForm'];
		$this->template->id = $id;
		if ($id) {
			$offer = $this->darknet->findOffer($id)->fetch();
			if (!$offer) {
				$this->error('Entry not found!');
			}
			$this->template->offer = $offer;
			$this->template->vendors = $this->darknet->findAllVendors()->fetchAll();
			$form->setDefaults($offer);
		}
		if ($vendorId != null) {
			$vendor = $this->darknet->findVendor($vendorId)->fetch();
			if (isset($vendor->name)) {
				$this->template->vendor = $vendor;
				$form->setValues([
					'vendor_id' => $vendor->id
				]);
			}
		}
	}

	public function actionDelete(int $id)
	{
		$offer = $this->darknet->findAllOffers()->get($id);
		if (!$offer) {
			$this->flashMessage('Entry not found!');
		} else {
			$this->flashMessage('Entry deleted!');
			$this->darknet->findAllOffers()->get($id)->delete();
		}
		$this->redirect('default');
	}

	protected function createComponentOfferForm()
	{
		$form = new Form;

		$drugList = [];
		$drugs = $this->darknet->findAll()->order('id')->fetchAll();
		foreach ($drugs as $id => $drug) {
			$drugList[$id] = $id . ' - ' . $drug->name;
		}
		$vendorList = [];
		$vendors = $this->darknet->findAllVendors()->order('level')->fetchAll();
		foreach ($vendors as $id => $vendor) {
			$vendorList[$id] = $vendor->name . ' - level ' . $vendor->level;
		}

		$form->addSelect('vendor_id', 'Vendor', $vendorList)
				->setHtmlAttribute('class', 'uk-select');

		$form->addSelect('drug_id', 'Drug', $drugList)
				->setHtmlAttribute('class', 'uk-select');

		$form->addInteger('base_quantity', 'Base quantity')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlAttribute('min', 100)
				->setHtmlAttribute('max', 1000000);

		$form->addInteger('quantity', 'Quantity')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlAttribute('min', 100)
				->setHtmlAttribute('max', 1000000);

		$form->addInteger('limit', 'limit')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlAttribute('min', 0)
				->setHtmlAttribute('max', 1000000);

		$form->addCheckbox('active', ' is active?')
				->setHtmlAttribute('class', 'uk-checkbox');

		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = [$this, 'offerFormSucceeded'];
		return $form;
	}

	public function offerFormSucceeded(Form $form, $values)
	{
		$id = (int) $this->getParameter('id');
		$primaryData = [];
		$primaryData['vendor_id'] = $values->vendor_id;
		$primaryData['drug_id'] = $values->drug_id;
		$primaryData['base_quantity'] = $values->base_quantity;
		$primaryData['quantity'] = $values->quantity;
		$primaryData['limit'] = $values->limit;
		$primaryData['active'] = $values->active;
		if ($id > 0) {
			$this->darknet->findOffer($id)->update($primaryData);
			$this->flashMessage('Offer successfully edited');
		} else {
			$this->darknet->findAllOffers()->insert($primaryData);
			$this->flashMessage('Offer added');
		}
	}
}
