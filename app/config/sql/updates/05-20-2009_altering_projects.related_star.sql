ALTER TABLE `projects` CHANGE `related_project_id` `based_on_pid` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `projects` DROP `related_username`;
