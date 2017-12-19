ALTER TABLE `mo_labels` ADD `site_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`, ADD INDEX `site_id` (`site_id`);
ALTER TABLE `mo_pages` ADD `site_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`, ADD INDEX `site_id` (`site_id`);
ALTER TABLE `mo_labels` DROP INDEX `name`, ADD INDEX `name` (`name`) USING BTREE;