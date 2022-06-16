<?php

declare(strict_types=1);

// Rename to VoteRewards
class VoteRewardsExample
{
	public array $voteCallback;

	public function __construct(array $voteCallback)
	{
		$this->voteCallback = $voteCallback;
	}
}

// Rename to VoteCallback
trait VoteCallbackExample
{
	public function actionYourMethod($param1, $param2): void
	{
		if ($param1 != null && $param2 != null) {
			if ($param1 === $this->voteRewards->voteCallback['hash'] && $param2 === $this->voteRewards->voteCallback['confirm']) {
				$username = $this->getHttpRequest()->getPost('uid');
				$player = $this->userRepository->getUserByName($username);
				$canVote = $this->userRepository->canVoteAgain($player->id);
				if ($canVote === true) {
					$voteReward = $this->userRepository->findAllVoteRewards()->where('user_id', $player->id);
					if (isset($voteReward->user_id)) {
						$voteReward->update([
							'last_reward' => 250000,
							'voted_at' => new DateTime(),
						]);
					} else {
						$this->userRepository->findAllVoteRewards()->insert([
							'user_id' => $player->id,
							'last_reward' => 250000,
							'voted_at' => new DateTime(),
						]);
					}
					$this->userRepository->addMoney($player->id, 250000);
					$this->sendJson(true);
				}
			}
		}

		$this->sendJson(false);
	}
}
