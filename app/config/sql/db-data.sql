--
-- Dumping data for table `notification_types`
--

INSERT INTO `notification_types` (`id`, `type`, `template`, `is_admin`) VALUES
(1, 'new_gcomment ', 'Your gallery <a href="/galleries/view/{gallery_id}?comment={comment_id}">{gallery_name}</a> has received a new comment from <a href="/users/{from_user_name}">{from_user_name}</a>.', '0'),
(2, 'new_pcomment ', 'Your project <a href="/projects/{to_user_name}/{project_id}?comment={comment_id}">{project_name}</a> has received a new comment by <a href="/users/{from_user_name}">{from_user_name}</a>.', '0'),
(3, 'new_gcomment_reply', 'Your comment on the gallery <a href="/galleries/view/{gallery_id}?comment={comment_id}">{gallery_name}</a> has been replied by <a href="/users/{from_user_name}">{from_user_name}</a>.', '0'),
(4, 'new_pcomment_reply ', 'Your comment on the project <a href="/projects/{project_owner_name}/{project_id}?comment={comment_id}">{project_name}</a> has been replied by <a href="/users/{from_user_name}">{from_user_name}</a>.', '0'),
(5, 'project_added_to_gallery', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has been added to the <a href="/galleries/view/{gallery_id}">{gallery_name}</a> gallery', '0'),
(6, 'pcomment_removed', 'Your comment "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community. Please read the <a  href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!\r\n<br/>\r\nThe Scratch Team has been informed of this and will decide to keep or lift the censorship', '0'),
(7, 'gcomment_removed', 'Your comment "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community. Please read the <a  href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!\r\n<br/>\r\nThe Scratch Team has been informed of this and will decide to keep or lift the censorship', '0'),
(8, 'inappropriate_pcomment', 'Your comment "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically. \r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(9, 'inappropriate_gcomment', 'Your comment "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(10, 'inappropriate_gcomment_reply', 'Your comment reply "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(11, 'inappropriate_pcomment_reply', 'Your comment reply "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(12, 'inappropriate_gtitle', 'The title  of your gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(13, 'inappropriate_ptitle', 'The title  of your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(14, 'inappropriate_gdesc', 'The description  of your gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(15, 'inappropriate_pdesc', 'The notes on your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(16, 'inappropriate_gtag', 'Your tag "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages when tagging a project. Please read the <a href="/terms">Terms of Use</a>', '0'),
(17, 'inappropriate_ptag', 'Your tag "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages when tagging a project. Please read the <a href="/terms">Terms of Use</a>', '0'),
(18, 'inappropriate_ptitle_upload', 'The title  of your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(19, 'inappropriate_pdesc_upload', 'The notes on your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>', '0'),
(20, 'inappropriate_ptag_upload', 'Your tag "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages when tagging a project. Please read the <a href="/terms">Terms of Use</a>', '0'),
(21, 'project_removed_auto', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community.\r\n<br/>\r\nPlease read the <a  href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!\r\n<br/>\r\nThe Scratch Team has been informed of this and will decide to keep or lift the censorship.', '0'),
(22, 'project_removed_admin', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community.\r\n<br/>\r\nPlease read the <a href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!', '0'),
(23, 'project_restored', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> was reviewed by the Scratch administrators and they decided to revert the automatic deletion caused by multiple Scratch members flagging your project as inappropriate. Please read the <a href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!', '0'),
(24, 'account_lock', 'Your account has been banned from the site due to violations of the Terms of Use.', '0'),
(25, 'language', 'Please we remind you to use appropriate language or your account would be blocked. Please we remind you to use appropriate language or your account would be blocked.', '1'),
(26, 'new_pcomment_reply_to_owner', '<a href="/users/{from_user_name}">{from_user_name}</a> replied to a comment on your project <a href="/projects/{project_owner_name}/{project_id}?comment={comment_id}">{project_name}</a>', '0'),
(27, 'new_gcomment_reply_to_owner', '<a href="/users/{from_user_name}">{from_user_name}</a> replied to a comment on your gallery <a href="/galleries/view/{gallery_id}?comment={comment_id}">{gallery_name}</a>', '0'),
(28, 'bulk', '', '1'),
(29, 'blank', '', '1');

INSERT INTO `permissions` (`id`, `name`, `url_name`) VALUES
(1, 'censor projects', 'censor_projects'),
(2, 'censor galleries', 'censor_galleries'),
(3, 'set projects as "for everyone"  or "not for everyone"', 'project_view_permission'),
(4, 'set galleries as "for everyone" or  "not for everyone"', 'galleries_view_permission'),
(5, 'feature projects', 'feature_projects'),
(6, 'feature galleries', 'feature_galleries'),
(7, 'delete project comments', 'delete_project_comments'),
(8, 'delete gallery comments', 'delete_gallery_comments'),
(9, 'block IP', 'block_IP'),
(10, 'block account', 'block_account');

INSERT INTO `relationship_types` (`id`, `name`, `description`, `timestamp`) VALUES
(1, 'friend', 'basic friendship', '2006-08-26 02:51:21');
