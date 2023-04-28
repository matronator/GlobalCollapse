ALTER TABLE `items`
ADD `energy` int NULL AFTER `armor`,
ADD `xp_boost` int NULL AFTER `energy`;

CREATE TABLE `player_gear_stats` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  `strength` int NOT NULL DEFAULT '0',
  `stamina` int NOT NULL DEFAULT '0',
  `speed` int NOT NULL DEFAULT '0',
  `attack` int NOT NULL DEFAULT '0',
  `armor` int NOT NULL DEFAULT '0',
  `energy` int NOT NULL DEFAULT '0',
  `xp_boost` int NOT NULL DEFAULT '0',
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `player_gear_stats`
CHANGE `energy` `max_energy` int NOT NULL DEFAULT '0' AFTER `armor`,
CHANGE `xp_boost` `xp_boost` double(8,4) NOT NULL DEFAULT '0' AFTER `max_energy`;

ALTER TABLE `items`
CHANGE `energy` `max_energy` int NULL AFTER `armor`,
CHANGE `xp_boost` `xp_boost` double(8,4) NULL AFTER `max_energy`;

ALTER TABLE `items`
CHANGE `max_energy` `energy_max` int NULL AFTER `armor`;

ALTER TABLE `player_gear_stats`
CHANGE `max_energy` `energy_max` int NOT NULL DEFAULT '0' AFTER `armor`;
