<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model;
use App\Model\UserRepository;
use App\Model\DrugsRepository;
use Nette\Application\UI\Form;
use DateTime;
use ActionLocker;
use App\Model\AssaultsRepository;
use App\Model\BuildingsRepository;
use App\Model\MiscRepository;
use App\Model\UnlockablesRepository;
use Timezones;
use Tracy\Debugger;

/////////////////////// FRONT: DEFAULT PRESENTER ///////////////////////

final class DefaultPresenter extends BasePresenter
{
	public const AVATAR_COUNT = 30;
	private const TRAINING_TIME = 5 * 60;

	private $userRepository;
	private $drugsRepository;
	private $unlockablesRepository;
	private $buildingsRepository;
	private AssaultsRepository $assaultsRepository;
	private MiscRepository $miscRepository;

	/** @var Model\ArticlesRepository */
  private $articleModel;

	public function __construct(
		UserRepository $userRepository,
		DrugsRepository $drugsRepository,
		Model\ArticlesRepository $articleModel,
		UnlockablesRepository $unlockablesRepository,
		BuildingsRepository $buildingsRepository,
		AssaultsRepository $assaultsRepository,
		MiscRepository $miscRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->drugsRepository = $drugsRepository;
		$this->articleModel = $articleModel;
		$this->unlockablesRepository = $unlockablesRepository;
		$this->buildingsRepository = $buildingsRepository;
		$this->assaultsRepository = $assaultsRepository;
		$this->miscRepository = $miscRepository;
	}

	protected function startup()
	{
			parent::startup();
	}

	public function renderDefault(?string $utm_source = null)
	{
		if ($utm_source) {
			$source = $this->miscRepository->findAllExternalVisits()->where('source', $utm_source)->fetch();
			if ($source) {
				$source->update([
					'visits+=' => 1,
					'last_visit' => new DateTime(),
				]);
			} else {
				$this->miscRepository->findAllExternalVisits()->insert([
					'source' => substr($utm_source, 0, 89),
					'visits' => 1,
					'last_visit' => new DateTime(),
				]);
			}
		}
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$this->template->user = $player;
			$avatars = [];
			for ($i = 1; $i <= 21; $i++) {
				$avatars[$i] = $i;
			}
			$articles = $this->articleModel->findAll()->select('*')->order('date DESC')->limit(2);
			$lastPage = 0;
			$data = [];
			foreach ($articles as $article) {
				$data[$article->id] = [
					'common' => $article,
					'translation' => $this->articleModel->findAllTranslations()->where('article_id', $article->id)->where('locale', 'en')->fetch()
				];
			}
			$this->template->articles = $data;

			$this->template->avatars = $avatars;
			$this->template->userAvatar = $player->avatar;
			$xp = $player->player_stats->xp;
			$this->template->xp = $xp;
			$xpMax = $player->player_stats->xp_max;
			$xpMin = $player->player_stats->xp_min;
			$this->template->xpMax = $xpMax;
			$this->template->xpMin = $xpMin;

			$drugsInventory = $this->drugsRepository->findDrugInventory($player->id)->order('drugs_id', 'ASC')->fetchAll();
			if (count($drugsInventory) > 0) {
				$this->template->drugsInventory = $drugsInventory;
			} else {
				$drugs = $this->drugsRepository->findAll();
				$this->template->drugs = $drugs;
			}

			// Leaderboard
			$page = 1;
			$itemsPerPage = 10;
			$usersRanked = $this->userRepository->findUsers();
			$position = 0;
			foreach ($usersRanked as $currentUser) {
				if ($currentUser->id == $player->id) {
					break;
				} else {
					$position++;
				}
			}
			$page = intval(floor($position / $itemsPerPage)) + 1;
			$lastPage = intval(round($this->userRepository->getTotalPlayers() / $itemsPerPage));
			$leaderboard = $this->userRepository->findUsers()->page($page, $itemsPerPage);
			$this->template->users = $leaderboard;
			$this->template->lastPage = $lastPage;
			$this->template->page = $page;
			$this->template->rankOffset = $position;
			$this->template->itemsPerPage = $itemsPerPage;
		} else {
			$drugs = $this->drugsRepository->findAll();
			$this->template->drugs = $drugs;
			$articles = $this->articleModel->findAll()->select('*')->order('date DESC')->limit(5);
			$lastPage = 0;
			$data = [];
			foreach ($articles as $article) {
				$data[$article->id] = [
					'common' => $article,
					'translation' => $this->articleModel->findAllTranslations()->where('article_id', $article->id)->where('locale', 'en')->fetch()
				];
			}
			$this->template->articles = $data;
		}
	}

	public function renderTraining()
	{
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			// $actionLocker = new ActionLocker();
			// $actionLocker->checkActions($player, $this);
			$this->template->user = $player;
			$xp = $player->player_stats->xp;
			$xpMax = $player->player_stats->xp_max;
			$xpMin = $player->player_stats->xp_min;
			$this->template->skillpoints = $player->skillpoints;
			$this->template->progressValue = round((($xp - $xpMin) / ($xpMax - $xpMin)) * (100));

			$strengthCost = round($player->player_stats->strength * pow(max(1, $player->player_stats->strength), 0.4));
			$staminaCost = round($player->player_stats->stamina * pow(max(1, $player->player_stats->stamina), 0.4));
			$speedCost = round($player->player_stats->speed * pow(max(1, $player->player_stats->speed), 0.4));
			$this->template->strengthCost = $strengthCost;
			$this->template->staminaCost = $staminaCost;
			$this->template->speedCost = $speedCost;
			$isTraining = $player->actions->training;
			$this->template->isTraining = $isTraining;
			if ($isTraining > 0) {
				$trainingUntil = $player->actions->training_end;
				$now = new DateTime();
				$diff = $trainingUntil->getTimestamp() - $now->getTimestamp();
				if ($diff >= 0) {
					$s = $diff % 60;
					$m = $diff / 60 % 60;
					$h = $diff / 3600 % 60;
					$this->template->hours = $h > 9 ? $h : '0'.$h;
					$this->template->minutes = $m > 9 ? $m : '0'.$m;
					$this->template->seconds = $s > 9 ? $s : '0'.$s;
					$this->template->trainingUntil = Timezones::getUserTime($trainingUntil, $this->userPrefs->timezone);
				} else {
					$this->endTraining($isTraining);
					$isTraining = 0;
					$this->redirect('this');
				}
			}
		} else {
			$this->redirect('Login:default');
		}
	}

	public function renderRest()
	{
		if ($this->user->isLoggedIn()) {
			$player = $this->userRepository->getUser($this->user->getIdentity()->id);
			$actionLocker = new ActionLocker();
			$actionLocker->checkActions($player, $this);
			$this->template->user = $player;
			$isResting = $player->actions->resting;
			$this->template->resting = $isResting;
			if ($isResting) {
				$restingSince = $player->actions->resting_start;
				$this->template->restingSince = Timezones::getUserTime($restingSince, $this->userPrefs->timezone, $this->userPrefs->dst);
				$nowDate = new DateTime();
				$diff = abs($restingSince->getTimestamp() - $nowDate->getTimestamp());
				$reward = intval(10 * round($diff / 1800));
				$newEnergy = $player->player_stats->energy + $reward;
				if ($newEnergy > $player->player_stats->energy_max) {
					$newEnergy = $player->player_stats->energy_max;
				}
				$this->template->energyGained = $reward;
				$this->template->newEnergy = $newEnergy;
				if ($diff < 3600) {
					$this->template->timePassed = round($diff / 60) . ' minutes';
				} else if ($diff <= 5400) {
					$this->template->timePassed = round($diff / 3600) . ' hour';
				} else {
					$this->template->timePassed = round($diff / 3600) . ' hours';
				}
			}
		} else {
			$this->redirect('Login:default');
		}
	}

	public function renderUnlockables()
	{
		if (!$this->user->isLoggedIn()) {
			$this->redirect('Login:default');
		}
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$land = $this->buildingsRepository->findPlayerLand($this->user->id)->fetch();
		$this->unlockablesRepository->checkUnlockables($player, $land);

		$unlocked = $this->unlockablesRepository->findPlayerUnlocked($this->user->getId())->order('unlocked_at')->fetchAll();
		$unlockedIds = array_column($unlocked, 'unlockables_id');
		$locked = $this->unlockablesRepository->findAll()->where('id NOT IN ?', !$unlockedIds ? [0] : $unlockedIds)->fetchAll();
		$assaults = $this->assaultsRepository->findPlayerAssaultStats($player->id)->fetch();

		$this->template->unlocked = $unlocked;
		$this->template->locked = $locked;
		$this->template->landLevel = isset($land->level) ? $land->level : 0;
		$this->template->buildingCount = $this->buildingsRepository->findPlayerBuildings($player->id)->count();
		$this->template->assaults = isset($assaults->total) ? $assaults : (object) [
			'attacks_won' => 0,
			'defenses_won' => 0,
			'attacks_lost' => 0,
			'defenses_lost' => 0,
			'total_attacks' => 0,
			'total_defenses' => 0,
			'total' => 0,
		];
	}

	public function createComponentRestForm(): Form {
		$form = new Form();
		$form->addSubmit('rest', 'Rest');
		$form->addSubmit('wakeup', 'Stop resting');
		$form->onSuccess[] = [$this, 'restFormSucceeded'];
		return $form;
	}

	public function restFormSucceeded(Form $form, $values): void {
		$control = $form->isSubmitted();
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$isResting = $player->actions->resting;
		if ($control->name === 'rest') {
			if (!$isResting) {
				$playerRestStart = new DateTime();
				$this->userRepository->getUser($player->id)->actions->update([
					'resting' => 1,
					'resting_start' => $playerRestStart
				]);
				$this->flashMessage($this->translate('general.messages.success.restStart'), 'success');
			}
		} else if ($control->name === 'wakeup') {
			if ($isResting) {
				$restingSince = $player->actions->resting_start;
				$nowDate = new DateTime();
				$diff = abs($restingSince->getTimestamp() - $nowDate->getTimestamp());
				$this->userRepository->getUser($player->id)->actions->update([
					'resting' => 0
				]);
				$reward = intval(10 * round($diff / 1800));
				if ($reward > 0) {
					if ($player->player_stats->energy + $reward > $player->player_stats->energy_max) {
						$this->userRepository->getUser($player->id)->player_stats->update([
							'energy' => $player->player_stats->energy_max
						]);
					} else {
						$this->userRepository->getUser($player->id)->player_stats->update([
							'energy+=' => $reward
						]);
					}
					$this->flashMessage($this->translate('general.messages.success.restEnd', ['reward' => $reward]), 'success');
				}
			}
		}
		$this->redirect('this');
	}

	private function endTraining($trainingStat) {
		switch ($trainingStat) {
			case 1:
				$this->userRepository->updateStatsAdd($this->user->getIdentity()->id, 1);
			break;
			case 2:
				$this->userRepository->updateStatsAdd($this->user->getIdentity()->id, 0, 1);
			break;
			case 3:
				$this->userRepository->updateStatsAdd($this->user->getIdentity()->id, 0, 0, 1);
			break;
		}
		$this->userRepository->getUser($this->user->getIdentity()->id)->actions->update([
			'training' => 0
		]);
	}

	public function createComponentTrainingForm(): Form {
		$form = new Form();
		$form->setHtmlAttribute('id', 'trainingForm');
		$form->addSubmit('strength', 'Train');
		$form->addSubmit('stamina', 'Train');
		$form->addSubmit('speed', 'Train');
		$form->onSuccess[] = [$this, 'trainingFormSucceeded'];
		return $form;
	}

	public function trainingFormSucceeded(Form $form, $values): void {
		$control = $form->isSubmitted();
		$trainNumber = 0;
		$trainSkill = '';
		switch ($control->name) {
			case 'strength':
				$trainNumber = 1;
				$trainSkill = 'strength';
			break;
			case 'stamina':
				$trainNumber = 2;
				$trainSkill = 'stamina';
			break;
			case 'speed':
				$trainNumber = 3;
				$trainSkill = 'speed';
			break;
		}
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		if ($player->actions->training == 0 && $trainNumber != 0) {
			// Training cost = skill level * 0.75
			$trainingCost = round($player->player_stats[$trainSkill] * 0.75);
			$currentMoney = $player->money;
			// Energy cost = 10
			$currentEnergy = $player->player_stats->energy;
			if ($currentMoney >= $trainingCost) {
				if ($currentEnergy >= 10) {
					$unlocked = $this->unlockablesRepository->findPlayerUnlocked($player->id)->where('unlockables.unlocks', 'faster_training')->order('unlockables.amount DESC')->limit(1)->fetch();
					$trainBoost = $this->unlockablesRepository->findAll()->where('id', $unlocked->unlockables_id)->fetch();
					$trainMultiplier = isset($trainBoost->amount) ? $trainBoost->amount : 100;
					$currentMoney -= $trainingCost;
					$currentEnergy -= 10;
					$now = new DateTime();
					$trainingEndTS = $now->getTimestamp();
					// Training time = 5 minutes = 300s
					$trainingEndTS += (int) round(self::TRAINING_TIME * (100 / $trainMultiplier), 0);
					$now->setTimestamp($trainingEndTS);
					$trainingEnd = $now->format('Y-m-d H:i:s');
					$this->userRepository->addMoney($player->id, -$trainingCost);
					$this->userRepository->getUser($player->id)->player_stats->update([
						'energy-=' => 10
					]);
					$this->userRepository->getUser($player->id)->actions->update([
						'training' => $trainNumber,
						'training_end' => $trainingEnd
					]);
					// $this->logger->addInfo($player->username . ' started ' . $trainSkill . ' training.');
					$this->flashMessage($this->translate('general.messages.success.trainingStart'), 'success');
				} else {
					$this->flashMessage($this->translate('general.messages.danger.notEnoughEnergy'), 'danger');
				}
			} else {
				$this->flashMessage($this->translate('general.messages.danger.notEnoughMoney'), 'danger');
			}
		}
		// $this->redirect('this');
		$this->redrawControl('training-form');
		$this->redrawControl('training-scripts');
	}

	public function createComponentSkillpointsForm(): Form {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$form = new Form();
		$form->setHtmlAttribute('id', 'skillpointsForm');
		$form->addHidden('strength', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'strength')
				 ->setHtmlId('hidden-1')
				 ->setDefaultValue(0)
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('stamina', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'stamina')
				 ->setHtmlId('hidden-2')
				 ->setDefaultValue(0)
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('speed', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'speed')
				 ->setHtmlId('hidden-3')
				 ->setDefaultValue(0)
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addHidden('usedSp', '0')
				 ->setHtmlAttribute('data-stat-hidden', 'skillpoints')
				 ->setHtmlId('hidden-4')
				 ->setDefaultValue(0)
				 ->setHtmlAttribute('data-extra-value', '0');
		$form->addSubmit('save', 'Confirm');
		$form->onSuccess[] = [$this, 'skillpointsFormSucceeded'];
		return $form;
	}

	public function skillpointsFormSucceeded(Form $form, $values): void {
		$player = $this->userRepository->getUser($this->user->getIdentity()->id);
		$strength = intval($values->strength);
		$stamina = intval($values->stamina);
		$speed = intval($values->speed);
		$usedSp = intval($values->usedSp);
		$statsTotal = $strength + $stamina + $speed;
		if ($usedSp > 0) {
			if ($statsTotal == $usedSp && $usedSp <= $player->skillpoints && $player->skillpoints > 0) {
				$this->userRepository->getUser($player->id)->update([
					'skillpoints-=' => $usedSp
				]);
				$this->userRepository->updateStatsAdd($player->id, $strength, $stamina, $speed);
				$this->flashMessage($this->translate('general.messages.success.skillpointsAssigned'), 'success');
			} else {
				$this->flashMessage($this->translate('general.messages.danger.invalidStats'), 'danger');
			}
		}
		$this->redirect('this');
	}

	public function createComponentAvatarForm(): Form
	{
		$avatars = [];
		for ($i = 1; $i <= self::AVATAR_COUNT; $i++) {
			$avatars[$i] = $i;
		}
		$form = new Form();
		$form->addRadioList('avatar', 'Choose an avatar from the list:', $avatars);
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = [$this, 'avatarFormSucceeded'];
		return $form;
	}

	public function avatarFormSucceeded(Form $form, $values): void {
		$selected = $values->avatar;
		if ($selected >= 1 && $selected <= self::AVATAR_COUNT) {
			$player = $this->user->getIdentity();
			if ($player) {
				$player->avatar = $selected;
				$this->userRepository->getUser($player->id)->update([
					'avatar' => $selected . ".jpg"
				]);
				$this->flashMessage($this->translate('general.messages.success.avatarChanged'), 'success');
			}
		}
		$this->redirect('this');
	}
}
