ALTER TABLE `notification_types` ADD `is_admin` BINARY NOT NULL DEFAULT '0';

ALTER TABLE `notification_types` ADD INDEX ( `is_admin` ) ;

INSERT INTO `notification_types` (`id` , `type` , `template` , `is_admin` ) VALUES ( NULL , 'blank', '', '1' );
