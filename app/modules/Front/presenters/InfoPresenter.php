<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Services\StripeService;
use VoteCallback;
use VoteRewards;

final class InfoPresenter extends BasePresenter
{
    use VoteCallback;

    /** @var Model\ArticlesRepository */
    private $articleModel;

    private VoteRewards $voteRewards;

    private StripeService $stripeService;

    public function __construct(
        Model\ArticlesRepository $articleModel,
        VoteRewards $voteRewards,
        StripeService $stripeService
    ) {
        parent::__construct();
        $this->articleModel = $articleModel;
        $this->voteRewards = $voteRewards;
        $this->stripeService = $stripeService;
    }

    protected function startup()
    {
        parent::startup();
    }

    public function renderDefault()
    {
    }

    public function renderNews(int $page = 1)
    {
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

    public function renderPost(string $htaccess)
    {
        $translation = $this->articleModel->findAllTranslations()->where('htaccess', $htaccess)->fetch();
        $article = $this->articleModel->findAll()->where('id', $translation->article_id)->fetch();
        $this->template->data = $article;
        $this->template->translation = $this->articleModel->findArticleTranslations($article->id)->where('locale', 'en')->fetch();
    }

    public function renderVoting()
    {
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
