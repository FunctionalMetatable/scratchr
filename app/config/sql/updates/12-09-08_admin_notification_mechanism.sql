ALTER TABLE `notification_types` ADD `is_admin` BINARY NOT NULL DEFAULT '0';

ALTER TABLE `notification_types` ADD INDEX ( `is_admin` ) ;