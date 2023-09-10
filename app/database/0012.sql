CREATE TABLE `statistics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  `jobs_completed` int unsigned NOT NULL DEFAULT '0',
  `times_rested` int unsigned NOT NULL DEFAULT '0',
  `times_scavenged` int unsigned NOT NULL DEFAULT '0',
  `buildings_collected` int unsigned NOT NULL DEFAULT '0',
  `money_from_jobs` int unsigned NOT NULL DEFAULT '0',
  `money_from_darknet` int unsigned NOT NULL DEFAULT '0',
  `money_from_scavenging` int unsigned NOT NULL DEFAULT '0',
  `money_from_assaults` int NOT NULL DEFAULT '0',
  `money_from_market` int unsigned NOT NULL DEFAULT '0',
  `money_to_market` int unsigned NOT NULL DEFAULT '0',
  `minutes_on_job` int unsigned NOT NULL DEFAULT '0',
  `minutes_rested` int unsigned NOT NULL DEFAULT '0',
  `minutes_scavenged` int unsigned NOT NULL DEFAULT '0',
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `statistics`
    ADD `items_bought` int unsigned NOT NULL DEFAULT '0',
    ADD `items_sold` int unsigned NOT NULL DEFAULT '0' AFTER `items_bought`;

ALTER TABLE `statistics`
    ADD `money_to_darknet` int unsigned NOT NULL DEFAULT '0' AFTER `money_from_darknet`;

ALTER TABLE `items`
    CHANGE `special_ability` `special_ability` text COLLATE 'utf8mb4_unicode_520_ci' NULL AFTER `xp_boost`;

ALTER TABLE `player_body`
    ADD `back` int NULL AFTER `body`;

ALTER TABLE `player_body`
    ADD FOREIGN KEY (`back`) REFERENCES `items` (`id`) ON DELETE SET NULL;

ALTER TABLE `player_stats`
    ADD `job_time_modifier` float NOT NULL DEFAULT '1';
