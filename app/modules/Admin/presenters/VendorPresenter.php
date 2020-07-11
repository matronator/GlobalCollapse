<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use DateTime;
use Nette,
	Nette\Application\UI\Form,
	App\Model;

final class VendorPresenter extends BasePresenter
{
	/** @var Model\DrugsRepository */
	public $darknet;

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
		$this->template->data = $this->darknet->findAllVendors()->order('level');
	}

	public function renderEdit(int $id = null)
	{
		$form = $this['vendorForm'];
		$this->template->id = $id;
		if ($id) {
			$vendor = $this->darknet->findVendor($id)->fetch();
			if (!$vendor) {
				$this->error('Entry not found!');
			}
			$this->template->vendor = $vendor;
			$this->template->offers = $this->darknet->findVendorOffers($id)->fetchAll();
			$form->setDefaults($vendor);
		}
	}

	public function actionDelete(int $id)
	{
		$vendor = $this->darknet->findAllVendors()->get($id);
		if (!$vendor) {
			$this->flashMessage('Entry not found!');
		} else {
			$this->flashMessage('Entry deleted!');
			$this->darknet->findAllVendors()->get($id)->delete();
		}
		$this->redirect('default');
	}

	protected function createComponentVendorForm()
	{
		$form = new Form;

		$form->addText('name', 'Name')
				->setHtmlAttribute('class', 'uk-input');

		$form->addInteger('level', 'Level')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlAttribute('min', 1)
				->setHtmlAttribute('max', 10);

		$form->addInteger('base_money', 'Base money')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlAttribute('min', 1000)
				->setHtmlAttribute('max', 2000000000);

		$form->addInteger('money', 'Current money')
				->setHtmlAttribute('class', 'uk-input')
				->setHtmlAttribute('min', 1000)
				->setHtmlAttribute('max', 2000000000);

		$form->addText('charge', 'Vendor\'s fee')
				->setHtmlAttribute('class', 'uk-input');

		$form->addCheckbox('active', ' is active?')
				->setHtmlAttribute('class', 'uk-checkbox');

		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = [$this, 'vendorFormSucceeded'];
		return $form;
	}

	public function vendorFormSucceeded(Form $form, $values)
	{
		$id = (int) $this->getParameter('id');
		$primaryData = [];
		$primaryData['name'] = $values->name;
		$primaryData['level'] = $values->level;
		$primaryData['base_money'] = $values->base_money;
		$primaryData['money'] = $values->money;
		$primaryData['charge'] = (float) $values->charge;
		$primaryData['active'] = $values->active;
		if ($id > 0) {
			$this->darknet->findVendor($id)->update($primaryData);
			$this->flashMessage('Vendor successfully edited');
		} else {
			$primaryData['active_since'] = new DateTime();
			$this->darknet->findAllVendors()->insert($primaryData);
			$this->flashMessage('Vendor added');
		}
	}
}
