ALTER TABLE `projects` CHANGE `related_project_id` `based_on_pid` INT( 10 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE `related_username` `based_on_uid` INT( 10 ) UNSIGNED NULL DEFAULT NULL 