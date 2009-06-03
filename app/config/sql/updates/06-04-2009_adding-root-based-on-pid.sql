ALTER TABLE `projects` ADD `root_based_on_pid` INT( 10 ) UNSIGNED NULL AFTER `based_on_pid` ;

ALTER TABLE `projects` ADD INDEX ( `root_based_on_pid` ) ;
