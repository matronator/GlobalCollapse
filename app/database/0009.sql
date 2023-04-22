ALTER TABLE `player_inventory`
RENAME TO `inventory_item`;

CREATE TABLE `player_inventory` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  `level` int NOT NULL DEFAULT '1',
  `width` int NOT NULL DEFAULT '5',
  `height` int NOT NULL DEFAULT '4',
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `inventory_item`
DROP FOREIGN KEY `inventory_item_ibfk_2`;

ALTER TABLE `inventory_item`
DROP INDEX `user_id`;

ALTER TABLE `inventory_item`
CHANGE `user_id` `player_inventory_id` int NOT NULL AFTER `item_id`,
ADD FOREIGN KEY (`player_inventory_id`) REFERENCES `player_inventory` (`id`) ON DELETE CASCADE;

ALTER TABLE `player_inventory`
CHANGE `width` `width` int NOT NULL DEFAULT '4' AFTER `level`,
CHANGE `height` `height` int NOT NULL DEFAULT '3' AFTER `width`;

ALTER TABLE `inventory_item`
CHANGE `x` `slot` int NOT NULL AFTER `player_inventory_id`,
DROP `y`;

ALTER TABLE `items`
ADD `image` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `type`;

ALTER TABLE `items`
ADD `subtype` varchar(64) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `type`;

ALTER TABLE `items`
ADD `built_in` tinyint NOT NULL,
ADD `created_at` datetime NULL AFTER `built_in`,
ADD `updated_at` datetime NULL AFTER `created_at`;

ALTER TABLE `player_body`
DROP FOREIGN KEY `player_body_ibfk_5`,
DROP FOREIGN KEY `player_body_ibfk_6`,
DROP INDEX `right_arm`,
DROP INDEX `left_arm`,
DROP `right_arm`,
DROP `left_arm`;

ALTER TABLE `player_body`
CHANGE `user_id` `user_id` int NULL AFTER `id`,
CHANGE `head` `head` int NULL AFTER `user_id`,
CHANGE `face` `face` int NULL AFTER `head`,
CHANGE `body` `body` int NULL AFTER `face`,
CHANGE `shoulders` `shoulders` int NULL AFTER `body`,
ADD `melee` int NULL AFTER `shoulders`,
ADD `ranged` int NULL AFTER `melee`,
ADD `shield` int NULL AFTER `ranged`,
CHANGE `legs` `legs` int NULL AFTER `shield`,
CHANGE `feet` `feet` int NULL AFTER `legs`,
ADD FOREIGN KEY (`melee`) REFERENCES `items` (`id`) ON DELETE CASCADE,
ADD FOREIGN KEY (`ranged`) REFERENCES `items` (`id`) ON DELETE CASCADE,
ADD FOREIGN KEY (`shield`) REFERENCES `items` (`id`) ON DELETE CASCADE;

ALTER TABLE `player_body`
CHANGE `user_id` `user_id` int NOT NULL AFTER `id`;
