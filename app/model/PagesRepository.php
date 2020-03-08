<?php

namespace App\Model;

use Nette;


class PagesRepository
{
	/** @var Nette\Database\Context */
	private $database;

	public $uploadDir = '/upload/pages/';

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function findAll()
	{
		return $this->database->table('page');
	}

	public function findAllTranslations()
    {
        return $this->database->table('page_translation');
    }

    public function findPageTranslations(int $pageId)
    {
        return $this->database->table('page_translation')->where('page_id', $pageId);
    }

	public function findAllImages()
	{
		return $this->database->table('page_image');
	}

	public function saveGallery(array $photos, int $id)
	{
		foreach ($photos as $photo) {
			$this->findAllImages()->insert(array('page_id' => $id, 'filename' => $photo));
		}
	}
}
