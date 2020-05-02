<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use DateTime;

/////////////////////// FRONT: BASE PRESENTER ///////////////////////
// Base presenter for all frontend presenters

class BasePresenter extends \App\BaseModule\Presenters\BasePresenter
{
	/** @persistent */
	public $locale;

	/** @var \Kdyby\Translation\Translator @inject */
	public $translator;

	/** @var \Monolog\Logger @inject */
  public $logger;

	/** @var Model\UserRepository */
	private $userRepository;

	public $contactFormFactory;

	public function injectRepository(
		Model\UserRepository $userRepository
	)
	{
		$this->userRepository = $userRepository;
	}

	protected function beforeRender()
	{
		$this->template->urlAbsolutePath = $this->getURL()->hostUrl;
		$this->template->urlFullDomain = $this->getURL()->host;
		$this->template->defaultLocale = $this->defaultLocale;
		// $this->template->user = (object) $this->user->getIdentity();
		$this->template->allPlayers = $this->userRepository->getTotalPlayers();
		$this->template->onlinePlayers = $this->userRepository->getOnlinePlayers();
		if ($this->user->isLoggedIn()) {
			$this->template->user = $this->userRepository->getUser($this->user->getIdentity()->id);
			$player = $this->user->getIdentity();
      $this->userRepository->getUser($player->id)->update([
				'last_active' => new DateTime()
			]);
		}
	}

	public function handleChangeLocale(string $locale) {
		$this->redirect('this', ['locale' => $locale]);
	}

	public function isAllowed($privilege, $resource = null)
	{
			$resource = $resource ? $resource : $this->name;
			return $this->user->isAllowed($resource, $privilege);
	}

	/**
   * log
   *
   * @param string|null $player  Name of the player
   * @param string|null $type  Use a predefined template message
   * @param string|null $msg  Custom message
   * @param array|null $extra  Any extra parameters (will go into context)
   * @return void
   */
  public function log(?string $player = null, ?string $type = null, ?array $extra = [], ?string $msg = ''): void
  {
    $message = $msg;
    if (isset($type)) {
      switch ($type) {
        case 'login':
          $message = 'has logged in.';
          break;
        case 'signup':
          $message = 'just signed up!';
          break;
        case 'logout':
          $message = 'has logged out.';
          break;
        case 'rest_start':
          $message = 'went to rest.';
          break;
        case 'rest_end':
          $message = 'woke up from a rest.';
          break;
        case 'train':
          $message = 'started training.';
          break;
        case 'train_end':
          $message = 'finished training.';
          break;
        case 'trainSP':
          $message = 'trained with skillpoints.';
          break;
        case 'avatar':
          $message = 'changed avatar.';
          break;
      }
    }
    if ($player) {
      $message = sprintf('%s ' . $message, $player);
		}
    $this->logger->info($message, $extra);
  }

}
