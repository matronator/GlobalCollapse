CREATE TABLE `page_hits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `last_update` datetime NULL ON UPDATE CURRENT_TIMESTAMP,
  `hits_this_month` int unsigned NOT NULL,
  `hits_total` bigint unsigned NOT NULL,
  `last_reset` datetime NULL
) ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci';
