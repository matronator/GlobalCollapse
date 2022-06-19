INSERT INTO `unlockables` (`type`, `unlocks`, `buildings_id`, `amount`, `unlock_at`, `image`, `expand_card`, `is_secret`)
SELECT 'land_level', 'building', '4', NULL, '6', 'poppy-field.jpg', '1', '0'
FROM `unlockables`
WHERE ((`id` = '1'));
