ALTER TABLE `actions`
ADD `offer_refreshes_used` tinyint unsigned NOT NULL DEFAULT '0' AFTER `offer_refreshed`;
