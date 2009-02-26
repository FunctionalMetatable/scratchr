--
-- Table structure for table `galleries_frontpage`
--

CREATE TABLE `galleries_frontpage` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('featured') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gallery_id_2` (`gallery_id`,`type`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `projects_frontpage`
--

CREATE TABLE `projects_frontpage` (
  `id` int(10) NOT NULL auto_increment,
  `project_id` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('top_loved','top_viewed','top_downloaded','top_remixed','featured','surprise','feature_gallery') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`type`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;