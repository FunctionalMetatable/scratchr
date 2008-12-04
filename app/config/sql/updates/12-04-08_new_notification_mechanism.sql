--

-- Rename notifications to oldnotifications

--

RENAME TABLE `notifications`  TO `oldnotifications` ;


--

-- Table structure for table `notifications`

--

 

CREATE TABLE `notifications` (

  `id` int(10) NOT NULL auto_increment,

  `to_user_id` int(10) NOT NULL,

  `from_user_name` varchar(20) collate utf8_unicode_ci default NULL,

  `project_id` int(10) default NULL,

  `project_owner_name` varchar(20) collate utf8_unicode_ci default NULL,

  `gallery_id` int(10) default NULL,

  `extra` varchar(64) collate utf8_unicode_ci default NULL,

  `notification_type_id` int(10) NOT NULL,

  `status` enum('READ','UNREAD') collate utf8_unicode_ci NOT NULL default 'UNREAD',

  `created` datetime NOT NULL,

  PRIMARY KEY  (`id`),

  KEY `to_user_id` (`to_user_id`,`project_id`,`gallery_id`,`notification_type_id`,`status`,`created`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 

--

-- Table structure for table `notification_types`

--

 

CREATE TABLE `notification_types` (

  `id` int(10) NOT NULL auto_increment,

  `type` varchar(127) collate utf8_unicode_ci NOT NULL,

  `template` text collate utf8_unicode_ci NOT NULL,

  PRIMARY KEY  (`id`),

  UNIQUE KEY `type_2` (`type`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 

--

-- Dumping data for table `notification_types`

--

 
INSERT INTO `notification_types` (`id`, `type`, `template`) VALUES

(1, 'new_gcomment ', 'Your gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a> has received a new comment from <a href="/users/{from_user_name}">{from_user_name}</a>.'),

(2, 'new_pcomment ', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has received a new comment by <a href="/users/{from_user_name}">{from_user_name}</a>.'),

(3, 'new_gcomment_reply', 'Your comment on the gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a> has been replied by <a href="/users/{from_user_name}">{from_user_name}</a>.'),

(4, 'new_pcomment_reply ', 'Your comment on the project <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> has been replied by <a href="/users/{from_user_name}">{from_user_name}</a>.'),

(5, 'project_added_to_gallery', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has been added to the <a href="/galleries/view/{gallery_id}">{gallery_name}</a> gallery'),

(6, 'pcomment_removed', 'Your comment "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community. Please read the <a  href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!\r\n<br/>\r\nThe Scratch Team has been informed of this and will decide to keep or lift the censorship'),

(7, 'gcomment_removed', 'Your comment "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community. Please read the <a  href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!\r\n<br/>\r\nThe Scratch Team has been informed of this and will decide to keep or lift the censorship'),

(8, 'inappropriate_pcomment', 'Your comment "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically. \r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(9, 'inappropriate_gcomment', 'Your comment "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(10, 'inappropriate_gcomment_reply', 'Your comment reply "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(11, 'inappropriate_pcomment_reply', 'Your comment reply "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(12, 'inappropriate_gtitle', 'The title  of your gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(13, 'inappropriate_ptitle', 'The title  of your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(14, 'inappropriate_gdesc', 'The description  of your gallery <a href="/galleries/view/{gallery_id}">{gallery_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(15, 'inappropriate_pdesc', 'The notes on your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(16, 'inappropriate_gtag', 'Your tag "%s" in <a href=''/galleries/view/{gallery_id}''>{gallery_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages when tagging a project. Please read the <a href="/terms">Terms of Use</a>'),

(17, 'inappropriate_ptag', 'Your tag "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages when tagging a project. Please read the <a href="/terms">Terms of Use</a>'),

(18, 'inappropriate_ptitle_upload', 'The title  of your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(19, 'inappropriate_pdesc_upload', 'The notes on your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> is potentially using inappropriate language. We remind you to use appropriate language for all ages, please read the <a href="/terms">Terms of Use</a>'),

(20, 'inappropriate_ptag_upload', 'Your tag "%s" in <a href="/projects/{project_owner_name}/{project_id}">{project_name}</a> was not posted because the system considered it inappropriate automatically.\r\n<br/>\r\nSometimes this automatic censorship is incorrect and we apologize if this is the case. We remind you to use appropriate language for all ages when tagging a project. Please read the <a href="/terms">Terms of Use</a>'),

(21, 'project_removed_auto', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community.\r\n<br/>\r\nPlease read the <a  href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!\r\n<br/>\r\nThe Scratch Team has been informed of this and will decide to keep or lift the censorship.'),

(22, 'project_removed_admin', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> has been removed because multiple Scratch members considered it inappropriate for the Scratch community.\r\n<br/>\r\nPlease read the <a href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!'),

(23, 'project_restored', 'Your project <a href="/projects/{to_user_name}/{project_id}">{project_name}</a> was reviewed by the Scratch administrators and they decided to revert the automatic deletion caused by multiple Scratch members flagging your project as inappropriate. Please read the <a href="/terms">Terms of Use</a> or contact us for more info. Thank you and Scratch on!'),

(24, 'account_lock', 'Your account has been banned from the site due to violations of the Terms of Use.');