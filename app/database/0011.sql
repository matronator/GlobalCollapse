ALTER TABLE `items`
ADD `market_drop_rate` double(10,5) unsigned NOT NULL DEFAULT '10' AFTER `stackable`;

CREATE TABLE `market` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `updated_at` datetime NULL
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

CREATE TABLE `market_items` (
  `market_id` int unsigned NOT NULL,
  `items_id` int NOT NULL,
  `count` int unsigned NOT NULL DEFAULT '1',
  FOREIGN KEY (`market_id`) REFERENCES `market` (`id`),
  FOREIGN KEY (`items_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `market`
ADD `fee` decimal(7,3) NOT NULL DEFAULT '0' AFTER `id`;

ALTER TABLE `items`
CHANGE `built_in` `built_in` tinyint NOT NULL DEFAULT '0' AFTER `market_drop_rate`,
ADD `generated` tinyint NOT NULL DEFAULT '0' AFTER `built_in`;

ALTER TABLE `items`
CHANGE `generated` `is_generated` tinyint NOT NULL DEFAULT '0' AFTER `built_in`,
ADD `was_generated_from` tinyint NOT NULL DEFAULT '0' AFTER `is_generated`;

ALTER TABLE `items`
CHANGE `was_generated_from` `children` int unsigned NOT NULL DEFAULT '0' AFTER `is_generated`;

ALTER TABLE `market`
ADD `level` int NOT NULL DEFAULT '1' AFTER `fee`;

ALTER TABLE `market_items`
ADD `market_slot` varchar(20) COLLATE 'utf8mb4_unicode_520_ci' NOT NULL;

ALTER TABLE `market_items`
ADD `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE `market_items`
DROP FOREIGN KEY `market_items_ibfk_1`,
ADD FOREIGN KEY (`market_id`) REFERENCES `market` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
