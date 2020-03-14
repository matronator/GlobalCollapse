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
                'translation' => $this->articles->findAllTranslations()->where('article_id', $article->id)->where('locale', $this->defaultLocale)->fetch()
            ];
        }

        $this->template->data = $data;
	}


	public function renderEdit(int $id)
    {
        $form = $this['articleForm'];

        $row = $this->articles->findAll()->wherePrimary($id)->fetch();

        if ($row) {
            $defaults = [];
            $defaults['date'] = $row->date->format('d.m.Y H:i:s');

            $translations = $this->articles->findArticleTranslations($id)->fetchAll();

            foreach ($translations as $translation) {
                $defaults['title_'.$translation->locale] = $translation->title;
                $defaults['htaccess_'.$translation->locale] = $translation->htaccess;
                $defaults['perex_'.$translation->locale] = $translation->perex;
                $defaults['text_'.$translation->locale] = $translation->text;
            }

            foreach ($this->localeList as $locale) {
                $tags = $this->articles->findAllTags()->select('title')->where('article_id', $id)->where('locale', $locale)->fetchAssoc('title');
                $defaults['tags_'.$locale] = implode(',', array_keys($tags));
            }

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
        $photos = $this->articles->findAllImages()->where('article_id', $id);

        if (!$row) {
            $this->flashMessage('Záznam nenalezen!');
        } else {

			if($row->image) {
				$this->imageStorage->delete($row->image, $this->articles->uploadDir);
			}

			foreach ($photos as $photo) {
				$this->imageStorage->delete($photo, $this->articles->uploadDir);
				$this->articles->findAllImages()->where('article_id', $photo)->delete();
			}

            $this->articles->findArticleTranslations($id)->delete();
            $this->articles->findAllTags()->where('article_id', $id)->delete();
            $this->articles->findAll()->where('id', $id)->delete();

            $this->flashMessage('Záznam úspěšně smazán!');
        }

        $this->redirect('default');
    }

	public function actionDeletePhoto(int $id)
	{
		$row = $this->articles->findAllImages()->get($id);

		if (!$row) {
			$this->flashMessage('Záznam nenalezen!');
		} else {
			$this->flashMessage('Záznam úspěšně smazán!');
			$this->articles->findAllImages()->wherePrimary($id)->delete();
			$this->imageStorage->delete($row->filename, $this->articles->uploadDir);
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

        foreach ( $this->localeList as $lang ) {

            $form->addText('title_'.$lang, $lang.': Název');
            $form->addText('htaccess_'.$lang, $lang.': URL');
            $form->addTextArea('perex_'.$lang, $lang.': Perex')
                ->setAttribute('class', 'ckeditor');
            $form->addTextarea('text_'.$lang, $lang.': Text')
                ->setAttribute('class', 'ckeditor');

            $form->addText('tags_'.$lang, $lang.': Štítky');
        }

        $form->addText('date', 'Datum')
            ->setDefaultValue(date('d.m.Y H:i:00'));

        $form->addUpload('image', 'Obrázek');

        $form->addMultiUpload('files', 'Soubory:');

        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = [$this, 'articleFormSucceeded'];
        return $form;
    }


	public function articleFormSucceeded(Form $form, $values)
    {
        $id = (int) $this->getParameter('id');

        // Insert primary record
        $primaryData = [];


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
            $this->flashMessage('Nevložili jste titulek článku. Data nemohla být uložena.');
            $this->redirect('default');
        }

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

        // Insert translations
        foreach ( $this->localeList as $lang ) {
            $this->articles->findAllTranslations()->insert([
                'article_id' => $articleId,
                'locale' => $lang,
                'title' => $values->{'title_'.$lang},
                'perex' => $values->{'perex_'.$lang},
                'text' => $values->{'text_'.$lang},
                'htaccess' => Strings::webalize($values->{'title_'.$lang}),
                'updated_at' => new DateTime()
            ]);

            // Insert tags
            $tags = explode(',', $values->{'tags_'.$lang});
            foreach ($tags as $tag) {
                if ($tag)
                    $this->articles->findAllTags()->insert([
                        'article_id' => $articleId,
                        'locale' => $lang,
                        'title' => trim($tag),
                        'htaccess' => Strings::webalize($tag),
                        'updated_at' => new DateTime()
                    ]);
            }
        }


        // Redirect
        $this->redirect('default');

    }
}
