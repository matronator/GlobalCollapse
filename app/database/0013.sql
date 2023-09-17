ALTER TABLE `user`
ADD `tier` int NOT NULL DEFAULT '1' AFTER `role`;

ALTER TABLE `user`
ADD `bitcoins` int unsigned NOT NULL DEFAULT '0' AFTER `money`;

ALTER TABLE `user`
ADD `stripe_customer_id` int unsigned NULL AFTER `actions_id`;

ALTER TABLE `user`
CHANGE `stripe_customer_id` `stripe_customer_id` varchar(255) NULL AFTER `actions_id`;

ALTER TABLE `user`
ADD `stripe_subscription_id` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `stripe_customer_id`;

ALTER TABLE `user`
CHANGE `stripe_customer_id` `stripe_customer_id` varchar(255) COLLATE 'utf8mb4_unicode_520_ci' NULL AFTER `actions_id`,
CHANGE `stripe_subscription_id` `stripe_subscription_id` varchar(255) COLLATE 'utf8mb4_unicode_520_ci' NULL AFTER `stripe_customer_id`;

CREATE TABLE `stripe_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `data` json NOT NULL
) COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `stripe_orders`
ADD `status` varchar(90) NOT NULL DEFAULT 'awaiting payment' AFTER `id`;
