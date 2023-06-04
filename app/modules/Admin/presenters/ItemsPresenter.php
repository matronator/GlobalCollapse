<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Components\DataTable\DataTable;
use App\Components\DataTable\DataTableFactory;
use App\Model\Entity\Item;
use App\Model\ItemsRepository;
use DateTime;
use ImageStorage;
use Nette,
	Nette\Application\UI\Form,
	App\Model;
use Nette\Utils\Html;

final class ItemsPresenter extends BasePresenter
{
	/** @var Model\ItemsRepository */
	private $itemsRepository;

	private ImageStorage $imageStorage;

	private DataTableFactory $dataTableFactory;

	public function __construct(Model\ItemsRepository $itemsRepository, ImageStorage $imageStorage, DataTableFactory $dataTableFactory)
	{
		$this->itemsRepository = $itemsRepository;
		$this->imageStorage = $imageStorage;
		$this->dataTableFactory = $dataTableFactory;
	}

	protected function startup()
	{
		parent::startup();
	}

	/*********************** RENDER VIEWS ***********************/

	public function createComponentDataTable(): DataTable
	{
		$dataTable = $this->dataTableFactory->create();
		$dataTable->setDataSource($this->itemsRepository->findAll());

		$dataTable->addColumn('image', '')
			->setRenderer(function ($row) {
				$url = $this->template->basePath;
				if ($row->built_in) {
					$url .= ItemsRepository::IMAGES_DIR . '/' . $row->image;
				} else {
					$url .= ItemsRepository::IMAGES_UPLOAD_DIR . '/' . $row->image;
				}
				$imgEl = Html::el('img', ['src' => $url, 'width' => 48, 'height' => 48, 'alt' => $row->description]);
				return $imgEl->toHtml();
			});

		$dataTable->addColumn('name', 'Name')
			->setSortable()
			->setHtmlClass('uk-table-link uk-link-reset uk-text-bold')
			->setRenderer(function ($row) {
				$aEl = Html::el('a', ['href' => $this->link('edit', $row->id)]);
				$aEl->setText($row->name);
				return $aEl->toHtml();
			});

		$dataTable->addColumn('type', 'Type')
			->setSortable()
			->setRenderer(function ($row) {
				return ucfirst($row->type);
			});

		$dataTable->addColumn('subtype', 'Subtype')
			->setSortable()
			->setRenderer(function ($row) {
				return ucfirst($row->subtype);
			});

		$dataTable->addColumn('cost', 'Cost')
			->setSortable();

		$dataTable->addColumn('unlock_at', 'Level')
			->setSortable();

		$dataTable->addColumn('attack', 'ATK')
			->setHtml('<span uk-icon="sword"></span>')
			->setSortable();

		$dataTable->addColumn('armor', 'ARM')
			->setHtml('<span uk-icon="shield"></span>')
			->setSortable();

		$dataTable->addColumn('strength', 'STR')
			->setHtml('<span uk-icon="strength"></span>')
			->setSortable();

		$dataTable->addColumn('stamina', 'STA')
			->setHtml('<span uk-icon="heart"></span>')
			->setSortable();

		$dataTable->addColumn('speed', 'SPD')
			->setHtml('<span uk-icon="speed"></span>')
			->setSortable();

		$dataTable->addColumn('energy_max', 'ENG')
			->setHtml('<span uk-icon="bolt"></span>')
			->setSortable();

		$dataTable->addColumn('xp_boost', 'XP')
			->setSortable();

		return $dataTable;
	}

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

		$form->addSelect('rarity', 'Rarity', Item::RARITIES)
			->setHtmlAttribute('class', 'uk-select')
			->setRequired('Please select a rarity.');

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

		$form->addInteger('energy_max', 'Max. energy')
			->setHtmlAttribute('class', 'uk-input')
			->setHtmlAttribute('min', 0);

		$form->addInteger('xp_boost', 'XP Boost')
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
		$primaryData['rarity'] = $values->rarity;
		$primaryData['unlock_at'] = $values->unlock_at;
		$primaryData['cost'] = $values->cost;
		$primaryData['strength'] = $values->strength;
		$primaryData['stamina'] = $values->stamina;
		$primaryData['speed'] = $values->speed;
		$primaryData['attack'] = $values->attack;
		$primaryData['armor'] = $values->armor;
		$primaryData['energy_max'] = $values->energy_max;
		$primaryData['xp_boost'] = $values->xp_boost;
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
