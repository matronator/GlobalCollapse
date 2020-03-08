-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Pát 08. úno 2019, 12:56
-- Verze serveru: 5.7.21
-- Verze PHP: 7.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `visu_cms`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date` DATETIME,
  `image` VARCHAR(64) COLLATE utf8_czech_ci,
  `visible` BOOLEAN COLLATE utf8_czech_ci DEFAULT 0,
  `updated_at` DATETIME,
  `created_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `article_tag`
--

DROP TABLE IF EXISTS `article_tag`;
CREATE TABLE IF NOT EXISTS `article_tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `article_id` INT(11),
  `locale` char(2) COLLATE utf8_czech_ci NOT NULL,
  `title` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `htaccess` varchar(256) COLLATE utf8_czech_ci NOT NULL,
  `updated_at` DATETIME,
  `created_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `article_translation`
--

DROP TABLE IF EXISTS `article_translation`;
CREATE TABLE IF NOT EXISTS `article_translation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `article_id` INT(11),
  `locale` char(2) COLLATE utf8_czech_ci NOT NULL,
  `title` varchar(512) COLLATE utf8_czech_ci,
  `perex` text COLLATE utf8_czech_ci,
  `text` text COLLATE utf8_czech_ci,
  `htaccess` varchar(512) COLLATE utf8_czech_ci,
  `updated_at` DATETIME,
  `created_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) NOT NULL DEFAULT 0,
  `level` INT(11) NOT NULL DEFAULT 0,
  `order` INT(11) NOT NULL DEFAULT 9999,
  `type` varchar(30) COLLATE utf8_czech_ci NOT NULL DEFAULT 'content',
  `image` VARCHAR(64) COLLATE utf8_czech_ci,
  `visible` BOOLEAN COLLATE utf8_czech_ci DEFAULT 0,
  `url` text COLLATE utf8_czech_ci,
  `updated_at` DATETIME,
  `created_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `page_translation`;
CREATE TABLE IF NOT EXISTS `page_translation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `page_id` INT(11),
  `locale` char(2) COLLATE utf8_czech_ci NOT NULL,
  `title` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `perex` text COLLATE utf8_czech_ci,
  `text` text COLLATE utf8_czech_ci,
  `htaccess` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `updated_at` DATETIME,
  `created_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `page_image`
--

DROP TABLE IF EXISTS `page_image`;
CREATE TABLE IF NOT EXISTS `page_image` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `page_id` INT(11) NOT NULL,
  `filename` varchar(128) COLLATE utf8_czech_ci,
  `updated_at` DATETIME,
  `created_at` DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `firstname` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `lastname` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `username` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `ip` varchar(30) COLLATE utf8_czech_ci,
  `last_log` datetime NOT NULL,
  `registration` datetime NOT NULL,
  `role` varchar(2) COLLATE utf8_czech_ci NOT NULL DEFAULT 'u',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `password`, `firstname`, `lastname`, `username`, `ip`, date_log, `registration`, `role`) VALUES
(0, '$2y$10$PuPWZACCJGqG8Z6b472sxuEgbxu2au8TuhMwjWoumO3SdEeb91j4a', 'Admin', 'Účet', 'info@visualio.cz', '::1', '2019-01-28 15:19:55', '2017-01-24 00:00:00', 'a');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE `contact_form` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `data` text NOT NULL,
 `datetime` datetime NOT NULL,
 `ip` varchar(40) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8