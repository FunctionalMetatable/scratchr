CREATE TABLE `anon_view_stats` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`project_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
)


ALTER TABLE `projects` ADD `anonviews` INT NULL AFTER `views` ;