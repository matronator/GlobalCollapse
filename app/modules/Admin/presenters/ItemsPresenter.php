<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\Entity\Item;
use App\Model\ItemsRepository;
use DateTime;
use ImageStorage;
use Nette,
	Nette\Application\UI\Form,
	App\Model;

final class ItemsPresenter extends BasePresenter
{
	/** @var Model\ItemsRepository */
	private $itemsRepository;
	
	private ImageStorage $imageStorage;

	public function __construct(Model\ItemsRepository $itemsRepository, ImageStorage $imageStorage)
	{
		$this->itemsRepository = $itemsRepository;
		$this->imageStorage = $imageStorage;
	}

	protected function startup()
	{
		parent::startup();
	}

	/*********************** RENDER VIEWS ***********************/

	public function renderDefault()
	{
		$this->template->data = $this->itemsRepository->findAll()->fetchAll();
		$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
		$this->template->imagesDir = ItemsRepository::IMAGES_DIR;
	}

	public function renderEdit(?int $id = null)
	{
		$form = $this['itemForm'];
		$this->template->id = $id;
		if ($id) {
			$item = $this->itemsRepository->get($id);
			if (!$item) {
				$this->flashMessage('Entry not found!', 'danger');
				$this->redirect('default');
			}
			$this->template->item = $item;
			$form->setDefaults($item->toArray());
		}
		$this->template->uploadDir = ItemsRepository::IMAGES_UPLOAD_DIR;
		$this->template->imagesDir = ItemsRepository::IMAGES_DIR;
	}

	public function actionDelete(int $id)
	{
		$item = $this->itemsRepository->get($id);
		if (!$item) {
			$this->flashMessage('Entry not found!');
		} else {
			$this->flashMessage('Entry deleted!');
			$this->itemsRepository->get($id)->delete();
		}
		$this->redirect('default');
	}

	protected function createComponentItemForm()
	{
		$form = new Form;

		$id = $this->getParameter('id');

		$form->addText('name', 'Name')
			->setHtmlAttribute('class', 'uk-input')
			->setRequired('Please enter name.');

		$form->addText('description', 'Description')
			->setHtmlAttribute('class', 'uk-input')
			->setRequired('Please enter description.');

		$form->addSelect('type', 'Type', Item::ITEM_TYPES)
			->setHtmlAttribute('class', 'uk-select')
			->setRequired('Please select type.');

		$form->addSelect('subtype', 'Subtype', Item::ITEM_SUBTYPES)
			->setHtmlAttribute('class', 'uk-select')
			->setRequired('Please select a subtype.');

		$image = $form->addUpload('image', 'Image');
		if (!$id) {
			$image->setRequired('Please select an image.');
		}

		$form->addInteger('unlock_at', 'Level to unlock')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 1)
			->setRequired('Please enter level to unlock.');

		$form->addInteger('cost', 'Cost')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addInteger('strength', 'Strength')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addInteger('stamina', 'Stamina')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addInteger('speed', 'Speed')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addInteger('attack', 'Attack')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addInteger('armor', 'Armor')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addCheckbox('stackable', ' Can the item be stacked?')
			->setHtmlAttribute('class', 'uk-checkbox');

		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = [$this, 'itemFormSucceeded'];
		return $form;
	}

	public function itemFormSucceeded(Form $form, $values)
	{
		$id = (int) $this->getParameter('id');
		$primaryData = [];

		$primaryData['name'] = $values->name;
		$primaryData['description'] = $values->description;
		$primaryData['type'] = $values->type;
		$primaryData['subtype'] = $values->subtype;
		$primaryData['unlock_at'] = $values->unlock_at;
		$primaryData['cost'] = $values->cost;
		$primaryData['strength'] = $values->strength;
		$primaryData['stamina'] = $values->stamina;
		$primaryData['speed'] = $values->speed;
		$primaryData['attack'] = $values->attack;
		$primaryData['armor'] = $values->armor;
		$primaryData['stackable'] = $values->stackable;
		$primaryData['built_in'] = false;
		$primaryData['created_at'] = new DateTime();

		if ($values->image->isOk()) {
			if ($id) {
				$item = $this->itemsRepository->get($id);
				$this->imageStorage->delete($item->image, ItemsRepository::IMAGES_UPLOAD_DIR);
			}
			$primaryData['image'] = $this->imageStorage->saveWithName($values->image, $values->name, ItemsRepository::IMAGES_UPLOAD_DIR);
		}

		if ($id > 0) {
			$primaryData['updated_at'] = new DateTime();
			$this->itemsRepository->get($id)->update($primaryData);
			$this->flashMessage('Item successfully edited');
		} else {
			$this->itemsRepository->findAll()->insert($primaryData);
			$this->flashMessage('Item added');
		}

		$this->redirect('default');
	}
}
