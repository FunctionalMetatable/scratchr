 CREATE TABLE `view_frontpages` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);


 CREATE TABLE `view_galleries` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`gallery_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);


 CREATE TABLE `gallery_user_comments` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`gallery_id` INT( 10 ) UNSIGNED NOT NULL ,
`gcomment_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);

 CREATE TABLE `view_channels` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`channel` ENUM( 'recent', 'featured', 'topviewed', 'toploved', 'remixed', 'surprise', 'friends_latest' ) NOT NULL DEFAULT 'recent',
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);


 CREATE TABLE `view_projects` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`project_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
); 

CREATE TABLE `project_user_comments` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`project_id` INT( 10 ) UNSIGNED NOT NULL ,
`pcomment_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);

 CREATE TABLE `project_user_tags` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`project_id` INT( 10 ) UNSIGNED NOT NULL ,
`tag_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);


 CREATE TABLE `upload_projects` (
`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`project_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipaddress` BIGINT( 20 ) NOT NULL ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `id` )
);
ALTER TABLE `upload_projects` ADD `action` ENUM( 'upload', 'update' ) NOT NULL DEFAULT 'upload' AFTER `ipaddress` ;

ALTER TABLE `user_stats` ADD `ipaddress` BIGINT( 20 ) NULL ;

ALTER TABLE `user_stats` ADD `lastout` DATETIME NULL ;