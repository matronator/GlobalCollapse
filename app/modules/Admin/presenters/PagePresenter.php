<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model,
	Nette,
	Nette\Utils\Strings,
	Nette\Application\UI\Form,
	Nette\Utils\Image,
	Nette\Utils\DateTime;

final class PagePresenter extends BasePresenter
{
	/** @var Model\PagesRepository */
	private $pages;

	private $pages_select = [];

	/** @var \ImageStorage */
	public $imageStorage;

	public function __construct(
		Model\PagesRepository $pages,
		\ImageStorage $storage
	){
		$this->pages = $pages;
		$this->imageStorage = $storage;
	}

    protected function startup()
    {
        parent::startup();
    }



    /*********************** RENDER VIEWS ***********************/

	public function renderDefault()
	{

	}

	public function renderEdit(int $id)
	{
        $form = $this['pageForm'];

        $row = $this->pages->findAll()->wherePrimary($id)->fetch();

        if ($row) {
            $defaults = [];
            $defaults['parent_id'] = $row->parent_id;

            $translations = $this->pages->findPageTranslations($id)->fetchAll();

            foreach ($translations as $translation) {
                $defaults['title_'.$translation->locale] = $translation->title;
                $defaults['htaccess_'.$translation->locale] = $translation->htaccess;
                $defaults['perex_'.$translation->locale] = $translation->perex;
                $defaults['text_'.$translation->locale] = $translation->text;
            }

            $form->setDefaults($defaults);
            $this->template->row = $row;
            $this->template->dir = $this->pages->uploadDir;
            $this->template->image = $row['image'];
            // $this->template->gallery = $this->pages->findAllImages()->where('page_id', $id)->fetchAll();
        }

        $this->template->id = $id;

    }

	/*********************** ACTIONS ***********************/

	public function actionDelete(int $id)
    {
        $row = $this->pages->findAll()->get($id);
        $translations = $this->pages->findPageTranslations($id);
        $photos = $this->pages->findAllImages()->where('page_id', $id);

        if (!$row) {
            $this->flashMessage('Záznam nenalezen!');
        } else {

			if($row->image) {
				$this->imageStorage->delete($row->image, $this->pages->uploadDir);
			}

			foreach ($photos as $photo) {
				$this->imageStorage->delete($photo, $this->pages->uploadDir);
				$this->pages->findAllImages()->where('page_id', $photo)->delete();
			}

            $this->pages->findPageTranslations($id)->delete();
            $this->pages->findAllTags()->where('page_id', $id)->delete();
            $this->pages->findAll()->where('id', $id)->delete();

            $this->flashMessage('Záznam úspěšně smazán!');
        }

        $this->redirect('default');
    }

	public function actionDeletePhoto(int $id)
	{
		$row = $this->pages->findAllImages()->get($id);

		if (!$row) {
			$this->flashMessage('Záznam nenalezen!');
		} else {
			$this->flashMessage('Záznam úspěšně smazán!');
			$this->pages->findAllImages()->wherePrimary($id)->delete();
			$this->imageStorage->delete($row->filename, $this->pages->uploadDir);
		}

		$this->redirect('default');
	}

	public function actionUpdateSort(string $items = null)
	{
		foreach(explode(',', $items) as $n => $row){
			$this->pages->findAll()->wherePrimary($row)->update(array('order'=>$n));
		}
		$this->redirect('default');
	}

	public function handleShow(int $id, bool $visible)
    {
        $this->pages->findAll()->where('id', $id)->update(['visible' => !$visible]);
    }

	public function getPageItems(int $parentId)
	{
		return $this->pages->findAll()->where('parent_id', $parentId)->order('order ASC, id ASC')->fetchAll();
	}

    public function getSubItems(int $parentId)
    {
        $pages = $this->pages->findAll()->select('*')->where('parent_id', $parentId)->order('order ASC')->fetchAll();
        $data = [];
        foreach ($pages as $page) {
            $data[$page->id] = [
                'common' => $page,
                'translation' => $this->pages->findAllTranslations()->select('*')->where('page_id', $page->id)->where('locale', $this->defaultLocale)->fetch()
            ];
        }

        return $data;
    }

	public function getTreePageArray(int $parentId, int $id)
	{
		$sql = $this->pages->findAll()->where('parent_id = ? AND id != ?', $parentId, $id)->order('order ASC, id ASC');
		while (true) {
			$row = $sql->fetch();
			if (!$row) break;
			$trans = $this->pages->findAllTranslations()->select('title')->where('page_id', $row->id)->where('locale', $this->defaultLocale)->fetch();
			$this->pages_select[$row->id] = str_repeat("--- ", $row->level+1).' '.$trans->title;
			$this->getTreePageArray($row->id, $id);
		}
	}


	/*********************** COMPONENT FACTORIES ***********************/

	/**
	* Edit form factory.
	* @return Form
	*/
	protected function createComponentPageForm()
    {
        $form = new Form;
        $id = (int) $this->getParameter('id');

        foreach ( $this->localeList as $lang ) {

            $form->addText('title_'.$lang, $lang.': Název');
            $form->addText('htaccess_'.$lang, $lang.': URL');
            $form->addTextArea('perex_'.$lang, $lang.': Perex')
            	->setAttribute('class', 'ckeditor');
            $form->addTextarea('text_'.$lang, $lang.': Text')
                ->setAttribute('class', 'ckeditor');
        }

        $this->getTreePageArray(0, $id);
		$form->addSelect('parent_id', 'Nadřazená stránka', [0=>'Hlavní kategorie']+$this->pages_select);

		$form->addSelect('type', 'Typ stránky', ['content'=>'Obsahová stránka', 'link'=>'Odkaz']);

        $form->addUpload('image', 'Obrázek');

        $form->addMultiUpload('files', 'Soubory:');

        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = [$this, 'pageFormSucceeded'];
        return $form;
    }

	public function pageFormSucceeded(Form $form, $values)
    {
        $id = (int) $this->getParameter('id');
        $parentPage = $this->pages->findAll()->where('id', $values->parent_id)->fetch();

        // Insert primary record
        $primaryData = [];

        $primaryData['level'] = $parentPage ? $parentPage->level + 1 : 0;

        
        if( empty( trim($values->{'title_' . $this->defaultLocale})) ) { // default language title is not set
            foreach ($this->localeList as $lang) {
                if ( trim($values->{'title_' . $lang}) ) {
                    $title = $values->{'title_' . $lang};
                    break;
                }
            }
        } else {
            $title = $values->{'title_' . $this->defaultLocale};
        }

        if( !isset($title) ) {
            $this->flashMessage('Nevložili jste titulek stránky. Data nemohla být uložena.');
            $this->redirect('default');
        }

        // Upload image
        if ($values->image->isOk()) { // There is no error, the file uploaded with success
            $primaryData['image'] = $this->imageStorage->saveFile($values->image, $this->pages->uploadDir); //upload
        }

        // Insert / update primary data
        if ($id > 0) {
            $primaryData['updated_at'] = new DateTime();
            $this->pages->findAll()->wherePrimary($id)->update($primaryData);
            $this->pages->findPageTranslations($id)->delete();
            $this->flashMessage('Záznam byl úspěšně upraven.');
            $pageId = $id;
        } else {
            $primaryData['created_at'] = new DateTime();
            $row = $this->pages->findAll()->insert($primaryData);
            $this->flashMessage('Záznam byl úspěšně přidán.');
            $pageId = $row->id;
        }

        // Insert translations
        foreach ( $this->localeList as $lang ) {
            $this->pages->findAllTranslations()->insert([
                'page_id' => $pageId,
                'locale' => $lang,
                'title' => $values->{'title_'.$lang},
                'perex' => $values->{'perex_'.$lang},
                'text' => $values->{'text_'.$lang},
                'htaccess' => Strings::webalize($values->{'title_'.$lang}),
                'updated_at' => new DateTime()
            ]);
        }


        // Redirect
        $this->redirect('default');

    }
}
