ALTER TABLE `player_unlocked`
ADD `opened` int NOT NULL DEFAULT '0' AFTER `quantity`;

ALTER TABLE `unlockables`
ADD `image` varchar(90) COLLATE 'utf8mb4_unicode_520_ci' NULL;

TRUNCATE `unlockables`;
INSERT INTO `unlockables` (`id`, `type`, `unlocks`, `buildings_id`, `amount`, `unlock_at`, `image`) VALUES
(1,	'land_level',	'building',	3,	NULL,	3,	'ecstasy-lab.jpg'),
(2,	'level',	'max_energy',	NULL,	125,	5,	'max-energy.png'),
(3,	'level',	'max_energy',	NULL,	150,	10,	'max-energy.png'),
(4,	'level',	'max_energy',	NULL,	175,	15,	'max-energy.png'),
(5,	'level',	'max_energy',	NULL,	200,	20,	'max-energy.png');
