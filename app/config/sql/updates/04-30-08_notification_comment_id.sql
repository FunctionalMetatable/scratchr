ALTER TABLE `notifications` ADD `comment_id` INT( 10 ) NULL DEFAULT NULL AFTER `gallery_id` ;

ALTER TABLE `notifications` ADD INDEX ( `comment_id` ) ;

UPDATE `notification_types` SET `template` = 'Your gallery <a href="/galleries/view/{gallery_id}?comment={comment_id}">{gallery_name}</a> has received a new comment from <a href="/users/{from_user_name}">{from_user_name}</a>.' WHERE `notification_types`.`type` = 'new_gcomment' LIMIT 1 ;

UPDATE `notification_types` SET `template` = 'Your project <a href="/projects/{to_user_name}/{project_id}?comment={comment_id}">{project_name}</a> has received a new comment by <a href="/users/{from_user_name}">{from_user_name}</a>.' WHERE `notification_types`.`type` = 'new_pcomment' LIMIT 1 ;

UPDATE `notification_types` SET `template` = 'Your comment on the gallery <a href="/galleries/view/{gallery_id}?comment={comment_id}">{gallery_name}</a> has been replied by <a href="/users/{from_user_name}">{from_user_name}</a>.' WHERE `notification_types`.`type` = 'new_gcomment_reply ' LIMIT 1 ;

UPDATE `notification_types` SET `template` = 'Your comment on the project <a href="/projects/{project_owner_name}/{project_id}?comment={comment_id}">{project_name}</a> has been replied by <a href="/users/{from_user_name}">{from_user_name}</a>.' WHERE `notification_types`.`type` = 'new_pcomment_reply ' LIMIT 1 ;

UPDATE `notification_types` SET `template` = '<a href="/users/{from_user_name}">{from_user_name}</a> replied to a comment on your project <a href="/projects/{project_owner_name}/{project_id}?comment={comment_id}">{project_name}</a>' WHERE `notification_types`.`type` = 'new_pcomment_reply_to_owner' LIMIT 1 ;

UPDATE `notification_types` SET `template` = '<a href="/users/{from_user_name}">{from_user_name}</a> replied to a comment on your gallery <a href="/galleries/view/{gallery_id}?comment={comment_id}">{gallery_name}</a>' WHERE `notification_types`.`type` = 'new_gcomment_reply_to_owner' LIMIT 1 ;