CREATE TABLE `gambling_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  `game` varchar(60) NOT NULL,
  `result` varchar(4) NOT NULL,
  `bet_amount` int NOT NULL,
  `win_amount` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `gambling_logs`
ADD `game_result` varchar(90) COLLATE 'utf8mb4_unicode_520_ci' NULL AFTER `game`;
