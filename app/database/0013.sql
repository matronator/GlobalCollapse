ALTER TABLE `user`
ADD `tier` int NOT NULL DEFAULT '1' AFTER `role`;

ALTER TABLE `user`
ADD `bitcoins` int unsigned NOT NULL DEFAULT '0' AFTER `money`;
