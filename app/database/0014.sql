ALTER TABLE `stripe_orders`
ADD `stripe_id` varchar(255) COLLATE 'utf8mb4_unicode_520_ci' NOT NULL AFTER `status`;

ALTER TABLE `user`
ADD `paddle_customer_id` varchar(255) COLLATE 'utf8mb4_unicode_520_ci' NULL AFTER `stripe_subscription_id`,
ADD `paddle_subscription_id` varchar(255) COLLATE 'utf8mb4_unicode_520_ci' NULL AFTER `paddle_customer_id`;

CREATE TABLE `paddle_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `status` varchar(90) NOT NULL,
  `paddle_id` varchar(255) NOT NULL,
  `data` json NOT NULL
) COLLATE 'utf8mb4_unicode_520_ci';

ALTER TABLE `user`
ADD `stripe_subscription_end_date` datetime NULL AFTER `stripe_subscription_id`;

ALTER TABLE `user`
ADD `stripe_subscription_start_date` datetime NULL AFTER `stripe_subscription_id`;
