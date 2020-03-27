-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `image` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `article`;

DROP TABLE IF EXISTS `article_tag`;
CREATE TABLE `article_tag` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `locale` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `htaccess` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `article_tag`;

DROP TABLE IF EXISTS `article_translation`;
CREATE TABLE `article_translation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `locale` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `perex` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `htaccess` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `article_translation`;

DROP TABLE IF EXISTS `contact_form`;
CREATE TABLE `contact_form` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `contact_form`;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `drugs`;
CREATE TABLE `drugs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `min` int NOT NULL,
  `max` int NOT NULL,
  `past_price` int NOT NULL,
  `updated` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE `drugs`;
INSERT INTO `drugs` (`id`, `name`, `price`, `min`, `max`, `past_price`, `updated`) VALUES
(1,	'Weed',	11,	3,	20,	10,	'2020-03-19 19:30:49'),
(2,	'Ecstasy',	22,	5,	25,	15,	'2020-03-19 19:30:49'),
(3,	'Meth',	67,	25,	85,	40,	'2020-03-19 19:30:49'),
(4,	'Heroin',	105,	50,	100,	85,	'2020-03-23 10:20:51'),
(5,	'Coke',	148,	60,	150,	92,	'2020-03-23 10:21:00');

DROP TABLE IF EXISTS `drugs_inventory`;
CREATE TABLE `drugs_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `drugs_id` int NOT NULL,
  `quantity` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `drugs_id` (`drugs_id`),
  CONSTRAINT `drugs_inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drugs_inventory_ibfk_2` FOREIGN KEY (`drugs_id`) REFERENCES `drugs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE `drugs_inventory`;

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT '0',
  `level` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '9999',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'content',
  `image` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '0',
  `url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `page`;

DROP TABLE IF EXISTS `page_image`;
CREATE TABLE `page_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `filename` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `page_image`;

DROP TABLE IF EXISTS `page_translation`;
CREATE TABLE `page_translation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int DEFAULT NULL,
  `locale` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `perex` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `htaccess` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `page_translation`;

DROP TABLE IF EXISTS `player_stats`;
CREATE TABLE `player_stats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `level` int NOT NULL DEFAULT '1',
  `power` int NOT NULL DEFAULT '3',
  `stamina` int NOT NULL DEFAULT '7',
  `speed` int NOT NULL DEFAULT '2',
  `energy` int NOT NULL DEFAULT '100',
  `energy_max` int NOT NULL DEFAULT '100',
  `xp` int NOT NULL DEFAULT '0',
  `xp_min` int NOT NULL DEFAULT '0',
  `xp_max` int NOT NULL DEFAULT '150',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE `player_stats`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_log` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `registration` datetime NOT NULL,
  `role` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'u',
  `avatar` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '3',
  `money` int NOT NULL DEFAULT '25',
  `skillpoints` int NOT NULL DEFAULT '4',
  `scavenging` int NOT NULL DEFAULT '0',
  `scavenge_start` datetime DEFAULT NULL,
  `tutorial` int NOT NULL DEFAULT '0',
  `player_stats_id` int DEFAULT NULL,
  `on_mission` int NOT NULL DEFAULT '0',
  `mission_start` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player_stats_id` (`player_stats_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`player_stats_id`) REFERENCES `player_stats` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `user`;

-- 2020-03-27 10:58:58
