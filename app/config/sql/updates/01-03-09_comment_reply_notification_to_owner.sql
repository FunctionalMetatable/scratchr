INSERT INTO `notification_types` `type`, `template`, `is_admin`) VALUES
 ('new_pcomment_reply_to_owner', '<a href="/users/{from_user_name}">{from_user_name}</a> replied to a comment on your project <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a>', '0'),
('new_gcomment_reply_to_owner', '<a href="/users/{from_user_name}">{from_user_name}</a> replied to a comment on your gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a>', '0');
