<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use VoteCallback;
use VoteRewards;

final class InfoPresenter extends BasePresenter
{
  use VoteCallback;

  /** @var Model\ArticlesRepository */
  private $articleModel;

  private UserRepository $userRepository;

  private VoteRewards $voteRewards;

  public function __construct(
    Model\ArticlesRepository $articleModel,
    UserRepository $userRepository,
    VoteRewards $voteRewards
	){
    $this->articleModel = $articleModel;
    $this->userRepository = $userRepository;
    $this->voteRewards = $voteRewards;
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

  public function renderVoting() {
    $player = $this->userRepository->getUser($this->user->getIdentity()->id);
    $voted = $this->userRepository->canVoteAgain($player->id);
    if ($voted !== true) {
      $this->template->timeLeft = $voted;
      $this->template->voted = true;
    } else {
      $this->template->voted = false;
    }
  }
}
