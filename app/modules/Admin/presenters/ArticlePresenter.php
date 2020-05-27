<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model,
    Nette,
    Nette\Utils\Strings,
    Nette\Application\UI\Form,
    Nette\Utils\Image,
    Nette\Utils\DateTime;

/////////////////////// ADMIN: ARTICLES PRESENTER ///////////////////////

final class ArticlePresenter extends BasePresenter
{
	/** @var Model\ArticlesRepository */
	private $articles;

	/** @var \ImageStorage */
	public $imageStorage;

	public function __construct(
		Model\ArticlesRepository $articles,
		\ImageStorage $storage
	){
		$this->articles = $articles;
		$this->imageStorage = $storage;
	}

    protected function startup()
    {
        parent::startup();
    }

	/*********************** RENDER VIEWS ***********************/
	public function renderDefault()
	{
		$articles = $this->articles->findAll()->select('*')->order('date DESC')->fetchAll();
        $data = [];
        foreach ($articles as $article) {
            $data[$article->id] = [
                'common' => $article,
                'translation' => $this->articles->findAllTranslations()->where('article_id', $article->id)->where('locale', 'en')->fetch()
            ];
        }

        $this->template->data = $data;
	}


	public function renderEdit(int $id = null)
    {
        $form = $this['articleForm'];

        $row = $this->articles->findAll()->wherePrimary($id)->fetch();

        if ($row) {
            $defaults = [];
            $defaults['date'] = $row->date->format('d.m.Y H:i:s');

            $translations = $this->articles->findArticleTranslations($id)->fetchAll();

            foreach ($translations as $translation) {
                $defaults['title'] = $translation->title;
                $defaults['htaccess'] = $translation->htaccess;
                $defaults['perex'] = $translation->perex;
                $defaults['text'] = $translation->text;
            }

            $tags = $this->articles->findAllTags()->select('title')->where('article_id', $id)->where('locale', 'en')->fetchAssoc('title');
            $defaults['tags'] = implode(',', array_keys($tags));

            $form->setDefaults($defaults);
            $this->template->row = $row;
            $this->template->dir = $this->articles->uploadDir;
            $this->template->image = $row['image'];
            // $this->template->gallery = $this->articles->findAllImages()->where('article_id', $id)->fetchAll();
        }

        $this->template->id = $id;
    }

	/*********************** ACTIONS ***********************/

	public function actionDelete(int $id)
    {
        $row = $this->articles->findAll()->get($id);
        $translations = $this->articles->findArticleTranslations($id);

        if (!$row) {
            $this->flashMessage('Záznam nenalezen!');
        } else {

			if($row->image) {
				$this->imageStorage->delete($row->image, $this->articles->uploadDir);
			}

            $this->articles->findArticleTranslations($id)->delete();
            $this->articles->findAllTags()->where('article_id', $id)->delete();
            $this->articles->findAll()->where('id', $id)->delete();

            $this->flashMessage('Záznam úspěšně smazán!');
        }

        $this->redirect('default');
    }

	public function handleShow(int $id, bool $visible)
    {
        $this->articles->findAll()->where('id', $id)->update(['visible' => !$visible]);
    }

	/*********************** COMPONENT FACTORIES ***********************/
	/**
	* Edit form factory.
	* @return Form
	*/
	protected function createComponentArticleForm()
    {
        $form = new Form;

        $form->addText('title', 'Title')
            ->setHtmlAttribute('class', 'uk-input');
        $form->addTextarea('text', 'Text')
            ->setHtmlAttribute('class', 'ckeditor uk-textarea');

        $form->addText('tags', 'Tags')
            ->setHtmlAttribute('class', 'uk-input');

        $form->addText('date', 'Date')
            ->setDefaultValue(date('d.m.Y H:i:s'))
            ->setHtmlAttribute('class', 'uk-input');

        $form->addUpload('image', 'Image');

        // $form->addMultiUpload('files', 'Files:');

        $form->addSubmit('save', 'Save');
        $form->onSuccess[] = [$this, 'articleFormSucceeded'];
        return $form;
    }


	public function articleFormSucceeded(Form $form, $values)
    {
        $id = (int) $this->getParameter('id');

        // Insert primary record
        $primaryData = [];

        // Set htaccess & date
        $primaryData['date'] = DateTime::createFromFormat('d.m.Y H:i:s', $values->date);

        // Upload image
        if ($values->image->isOk()) { // There is no error, the file uploaded with success
            $primaryData['image'] = $this->imageStorage->saveFile($values->image, $this->articles->uploadDir); //upload
        }

        // Insert / update primary data
        if ($id > 0) {
            $primaryData['updated_at'] = new DateTime();
            $this->articles->findAll()->wherePrimary($id)->update($primaryData);
            $this->articles->findArticleTranslations($id)->delete();
            $this->articles->findAllTags()->where('article_id', $id)->delete();
            $this->flashMessage('Záznam byl úspěšně upraven.');
            $articleId = $id;
        } else {
            $primaryData['created_at'] = new DateTime();
            $row = $this->articles->findAll()->insert($primaryData);
            $this->flashMessage('Záznam byl úspěšně přidán.');
            $articleId = $row->id;
        }

        $this->articles->findAllTranslations()->insert([
            'article_id' => $articleId,
            'locale' => 'en',
            'title' => $values->title,
            'perex' => substr($values->text, 0, 50),
            'text' => $values->text,
            'htaccess' => Strings::webalize($values->title),
            'updated_at' => new DateTime()
        ]);
        $tags = explode(',', $values->{'tags'});
        // Insert tags
        foreach ($tags as $tag) {
            if ($tag)
                $this->articles->findAllTags()->insert([
                    'article_id' => $articleId,
                    'locale' => 'en',
                    'title' => trim($tag),
                    'htaccess' => Strings::webalize($tag),
                    'updated_at' => new DateTime()
                ]);
        }

        // Redirect
        $this->redirect('Article:default');
    }
}
