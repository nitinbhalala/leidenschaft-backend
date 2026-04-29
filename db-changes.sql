ALTER TABLE `customers` CHANGE `password` `password` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `order_items` CHANGE `customer_id` `customer_id` BIGINT UNSIGNED NULL DEFAULT NULL;