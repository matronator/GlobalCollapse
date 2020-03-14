<?php

namespace App\Model;

use Nette;
use \Kdyby\Translation\Translator;


class ArticlesRepository
{
    /** @var Translator */
    private $translator;

    /** @var string */
    private $defaultLocale;

	/** @var Nette\Database\Context */
	private $database;

	public $uploadDir = '/upload/articles/';

	public function __construct(
        Translator $translator,
        Nette\Database\Context $database
    )
	{
        $this->translator = $translator;
		$this->database = $database;

        $this->defaultLocale = $this->translator->getDefaultLocale();
	}

	public function findAll()
	{
		return $this->database->table('article');
	}

	public function findAllTranslations()
    {
        return $this->database->table('article_translation');
    }

    public function findArticleTranslations(int $articleId)
    {
        return $this->database->table('article_translation')->where('article_id', $articleId);
    }

    public function findAllWithTranslation(string $lang, $tag = null, int $limit = 9999)
    {

        $articles = [];

        if ( empty(array_keys($articles)) ) {

            $data = $this->database->query('
                SELECT article.*, article_translation.title, article_translation.perex, article_translation.locale
                FROM article
                LEFT JOIN article_translation
                ON article_translation.article_id = article.id
                WHERE (article_translation.locale = ?)
                AND article.visible = 1
                AND article_translation.title <> ""
                GROUP BY article.id
                ORDER BY article.date DESC
                LIMIT ?
            ', ($lang=='en' || $lang=='cs' ? $lang : $this->defaultLocale), $limit)->fetchAll();

        } else {

            $data = $this->database->query('
                SELECT article.*, article_translation.title, article_translation.perex, article_translation.locale
                FROM article
                LEFT JOIN article_translation
                ON article_translation.article_id = article.id
                WHERE (article_translation.locale = ?
                OR article_translation.locale = ?)
                AND article.id IN (?)
                AND article.visible = 1
                AND article_translation.title <> ""
                ORDER BY article.date DESC
                LIMIT ?
            ', $lang, $this->defaultLocale, array_keys($articles), $limit)->fetchAll();

        }

        return $data;

    }

	public function findAllImages()
	{
		return $this->database->table('article_images');
	}

	public function saveGallery(array $photos, int $id)
	{
		foreach ($photos as $photo) {
			$this->findAllImages()->insert(array('article_id' => $id, 'filename' => $photo));
		}
	}

}
