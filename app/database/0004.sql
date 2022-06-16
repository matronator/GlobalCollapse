CREATE TABLE `vote_rewards` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  `last_reward` int NOT NULL,
  `voted_at` datetime NOT NULL DEFAULT NOW(),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) COLLATE 'utf8mb4_unicode_520_ci';
