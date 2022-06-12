ALTER TABLE `unlockables`
ADD `is_secret` int NOT NULL DEFAULT '1';

INSERT INTO `unlockables` (`id`, `type`, `unlocks`, `buildings_id`, `amount`, `unlock_at`, `image`, `expand_card`, `is_secret`) VALUES
(7,	'attacks_count',	'faster_training',	NULL,	125,	25,	NULL,	1, 0),
(8,	'attacks_count',	'faster_training',	NULL,	150,	50,	NULL,	1, 0),
(9,	'attacks_count',	'faster_training',	NULL,	200,	100,	NULL,	1, 0);

UPDATE `unlockables` SET
`id` = '1',
`type` = 'land_level',
`unlocks` = 'building',
`buildings_id` = '3',
`amount` = NULL,
`unlock_at` = '3',
`image` = 'ecstasy-lab.jpg',
`expand_card` = '1',
`is_secret` = '0'
WHERE `id` = '1';

CREATE TABLE `external_visits` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `source` varchar(90) NOT NULL,
  `medium` varchar(90) NULL,
  `campaign` varchar(90) NULL
) COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `external_visits`
ADD `visits` int NOT NULL DEFAULT '0',
ADD `last_visit` datetime NULL ON UPDATE CURRENT_TIMESTAMP AFTER `visits`;
