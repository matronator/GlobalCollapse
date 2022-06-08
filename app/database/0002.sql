ALTER TABLE `unlockables`
CHANGE `type` `type` varchar(30) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
CHANGE `unlocks` `unlocks` varchar(64) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `type`;

ALTER TABLE `unlockables`
ADD `expand_card` int NOT NULL DEFAULT '1';

INSERT INTO `unlockables` (`type`, `unlocks`, `buildings_id`, `amount`, `unlock_at`, `image`, `expand_card`)
VALUES ('building_count', 'collect_all_buildings', NULL, NULL, '5', NULL, 0);

UPDATE `unlockables` SET
`id` = '6',
`type` = 'building_count',
`unlocks` = 'mass_collect_buildings',
`buildings_id` = NULL,
`amount` = NULL,
`unlock_at` = '5',
`image` = NULL,
`expand_card` = '0'
WHERE `id` = '6';
