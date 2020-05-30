<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use DateTime;

final class InfoPresenter extends BasePresenter
{
  /** @var Model\ArticlesRepository */
  private $articleModel;

  public function __construct(
    Model\ArticlesRepository $articleModel
	){
    $this->articleModel = $articleModel;
	}

	protected function startup()
	{
		parent::startup();
  }

  public function renderDefault() {

  }

  public function renderNews(int $page = 1) {
    $allArticles = $this->articleModel->findAll()->select('*')->order('date DESC');
    $lastPage = 0;
    $articles = $allArticles->page($page, 6, $lastPage);
    $data = [];
    foreach ($articles as $article) {
      $data[$article->id] = [
        'common' => $article,
        'translation' => $this->articleModel->findAllTranslations()->where('article_id', $article->id)->where('locale', 'en')->fetch()
      ];
    }
    $this->template->data = $data;
    $this->template->page = $page;
    $this->template->lastPage = $lastPage;
  }

  public function renderPost($id = 0) {
    $this->template->data = $this->articleModel->findAll()->where('id', $id)->fetch();
		$this->template->translation = $this->articleModel->findArticleTranslations($id)->where('locale', 'en')->fetch();
  }
}
