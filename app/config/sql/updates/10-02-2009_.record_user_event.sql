CREATE TABLE `user_events` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `ipaddress` bigint(20) NOT NULL,
  `time` datetime NOT NULL,
  `event` enum('view_frontpage','view_channel','view_gallery','view_project','do_gallery_comment','do_project_comment','do_project_tag','do_project_upload','do_project_update','do_login','do_logout') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
);