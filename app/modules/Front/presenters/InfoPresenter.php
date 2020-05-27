<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;

final class InfoPresenter extends BasePresenter
{
  /** @var Model\ArticlesRepository */
  private $articles;

  public function __construct(
		Model\ArticlesRepository $articles
	){
		$this->articles = $articles;
	}

	protected function startup()
	{
		parent::startup();
  }

  public function renderDefault() {

  }

  public function renderNews() {
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

  public function renderPost($id = 0) {
    $this->template->data = $this->articles->findAll()->where('id', $id)->fetch();
		$this->template->translation = $this->articles->findArticleTranslations($id)->where('locale', 'en')->fetch();
  }
}
