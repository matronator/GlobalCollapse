ALTER TABLE `items`
CHANGE `item` `name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
ADD `description` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL,
ADD `type` varchar(64) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `description`,
ADD `unlock_at` int NOT NULL AFTER `type`,
ADD `cost` int NOT NULL AFTER `unlock_at`,
ADD `stackable` tinyint NOT NULL AFTER `cost`;

ALTER TABLE `items_inventory`
ADD `x` int NOT NULL AFTER `user_id`,
ADD `y` int NOT NULL AFTER `x`;

CREATE TABLE `player_body` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int NOT NULL,
  `head` int NOT NULL,
  `face` int NOT NULL,
  `body` int NOT NULL,
  `right_arm` int NOT NULL,
  `left_arm` int NOT NULL,
  `shoulders` int NOT NULL,
  `legs` int NOT NULL,
  `feet` int NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`head`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`face`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`body`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`right_arm`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`left_arm`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shoulders`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`legs`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`feet`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `items_inventory`
RENAME TO `player_inventory`;
