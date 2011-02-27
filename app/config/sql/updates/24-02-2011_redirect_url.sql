CREATE TABLE `user_redirects` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT UNSIGNED NOT NULL ,
`unencoded_url` VARCHAR( 255 ) NULL ,
`referrer_url` VARCHAR( 255 ) NULL ,
`created` DATETIME NOT NULL ,
PRIMARY KEY ( `id` )
);



ALTER TABLE `blocked_users` ADD `unblock_date` DATE NULL DEFAULT '0000-00-00';