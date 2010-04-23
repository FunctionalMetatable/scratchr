-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Generation Time: Apr 23, 2010 at 09:43 AM
-- Server version: 5.0.86
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_comments`
--

CREATE TABLE IF NOT EXISTS `admin_comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `content` text character set latin1 NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=208 ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_tags`
--

CREATE TABLE IF NOT EXISTS `admin_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `tag_id` int(11) default NULL,
  `status` enum('active','inactive') character set latin1 default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `analysis_project_user_view`
--

CREATE TABLE IF NOT EXISTS `analysis_project_user_view` (
  `project_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `viewedYN` char(1) default NULL,
  `minViewTime` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `analysis_view_stats`
--

CREATE TABLE IF NOT EXISTS `analysis_view_stats` (
  `id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `ipaddress` bigint(20) default NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  KEY `analysis_view_stats_idx1` (`project_id`),
  KEY `analysis_view_stats_idx2` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `content` text character set latin1,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `isOn` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `anon_view_stats`
--

CREATE TABLE IF NOT EXISTS `anon_view_stats` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `ipaddress` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3836649 ;

-- --------------------------------------------------------

--
-- Table structure for table `apcomments`
--

CREATE TABLE IF NOT EXISTS `apcomments` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_ips`
--

CREATE TABLE IF NOT EXISTS `blocked_ips` (
  `id` int(11) NOT NULL auto_increment,
  `ip` bigint(20) default '0',
  `user_id` int(11) default '0',
  `reason` text collate latin1_general_ci,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=591 ;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_users`
--

CREATE TABLE IF NOT EXISTS `blocked_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `admin_id` int(11) default NULL,
  `reason` text collate latin1_general_ci,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1350 ;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_user_frontpages`
--

CREATE TABLE IF NOT EXISTS `blocked_user_frontpages` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `admin_id` int(11) default NULL,
  `reason` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cake_sessions`
--

CREATE TABLE IF NOT EXISTS `cake_sessions` (
  `id` varchar(255) NOT NULL default '',
  `data` varchar(1000) default NULL,
  `expires` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `closure_reasons`
--

CREATE TABLE IF NOT EXISTS `closure_reasons` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `ipaddress` bigint(20) NOT NULL,
  `reasons` text NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=142 ;

-- --------------------------------------------------------

--
-- Table structure for table `clubbed_galleries`
--

CREATE TABLE IF NOT EXISTS `clubbed_galleries` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL default '0',
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table `clubbed_galleries_copy`
--

CREATE TABLE IF NOT EXISTS `clubbed_galleries_copy` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL default '0',
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `clubbed_themes`
--

CREATE TABLE IF NOT EXISTS `clubbed_themes` (
  `id` int(10) NOT NULL auto_increment,
  `theme_id` int(10) NOT NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `curators`
--

CREATE TABLE IF NOT EXISTS `curators` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `visibility` enum('visible','deleted') NOT NULL default 'visible',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `downloaders`
--

CREATE TABLE IF NOT EXISTS `downloaders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `time` (`timestamp`),
  KEY `downloaders_user_id_idx` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1862587 ;

-- --------------------------------------------------------

--
-- Table structure for table `downloaders_copy`
--

CREATE TABLE IF NOT EXISTS `downloaders_copy` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `time` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=144371 ;

-- --------------------------------------------------------

--
-- Table structure for table `dusers`
--

CREATE TABLE IF NOT EXISTS `dusers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `firstname` char(25) NOT NULL,
  `lastname` char(25) NOT NULL,
  `email` char(100) NOT NULL,
  `role` char(255) default NULL,
  `organization` char(255) default NULL,
  `city` char(255) default NULL,
  `created` datetime default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `state` char(255) default NULL,
  `country` char(255) default NULL,
  `hearabout` varchar(255) default NULL,
  `interestsabout` varchar(255) default NULL,
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1403018 ;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=486741 ;

-- --------------------------------------------------------

--
-- Table structure for table `featured_galleries`
--

CREATE TABLE IF NOT EXISTS `featured_galleries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `gallery_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `theme_id` (`gallery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=250 ;

-- --------------------------------------------------------

--
-- Table structure for table `featured_projects`
--

CREATE TABLE IF NOT EXISTS `featured_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1071 ;

-- --------------------------------------------------------

--
-- Table structure for table `featured_themes`
--

CREATE TABLE IF NOT EXISTS `featured_themes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `theme_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `theme_id` (`theme_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `flaggers`
--

CREATE TABLE IF NOT EXISTS `flaggers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `reasons` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ipaddress` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `flaggers_idx2` (`project_id`),
  KEY `flaggers_idx1` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24323 ;

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE IF NOT EXISTS `friend_requests` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `to_id` int(10) NOT NULL,
  `status` enum('pending','accepted','declined') default NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `friend_request_user_id_idx` (`user_id`),
  KEY `friend_request_to_id` (`to_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=664922 ;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE IF NOT EXISTS `galleries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(100) NOT NULL,
  `type` int(10) NOT NULL default '1',
  `description` text,
  `user_id` int(10) unsigned NOT NULL default '0',
  `icon` varchar(255) default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP,
  `total_projects` int(10) NOT NULL default '0',
  `total_subscriptions` int(10) NOT NULL default '1',
  `changed` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('notreviewed','censored','safe','notsafe') NOT NULL default 'notreviewed',
  `visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm','suspended') NOT NULL default 'visible',
  `usage` enum('public','private','byinvite','friends') default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80216 ;

-- --------------------------------------------------------

--
-- Table structure for table `galleries_frontpage`
--

CREATE TABLE IF NOT EXISTS `galleries_frontpage` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('featured') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gallery_id_2` (`gallery_id`,`type`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=69 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_flags`
--

CREATE TABLE IF NOT EXISTS `gallery_flags` (
  `id` int(11) NOT NULL auto_increment,
  `gallery_id` int(11) NOT NULL,
  `violent` tinyint(1) NOT NULL default '0',
  `obscene` tinyint(1) NOT NULL default '0',
  `disrespectful` tinyint(1) NOT NULL default '0',
  `m_obscene` tinyint(1) NOT NULL default '0',
  `m_violent` tinyint(1) NOT NULL default '0',
  `mature` tinyint(1) NOT NULL default '0',
  `admin_id` int(11) NOT NULL,
  `feature_admin_id` int(11) default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `feature_timestamp` timestamp NULL default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=103 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_memberships`
--

CREATE TABLE IF NOT EXISTS `gallery_memberships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `gallery_id` int(10) unsigned NOT NULL default '0',
  `type` int(10) NOT NULL default '2',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `rank` enum('owner','curator','member','bookmarker','contributor') NOT NULL default 'bookmarker',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=309852 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_projects`
--

CREATE TABLE IF NOT EXISTS `gallery_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `gallery_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `galllery_id_project_id` (`gallery_id`,`project_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=760462 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_requests`
--

CREATE TABLE IF NOT EXISTS `gallery_requests` (
  `id` int(10) NOT NULL auto_increment,
  `subscriber_id` int(10) NOT NULL default '0',
  `owner_id` int(10) NOT NULL default '0',
  `gallery_id` int(10) default NULL,
  `status` enum('pending','accepted','declined') default NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33366 ;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_tags`
--

CREATE TABLE IF NOT EXISTS `gallery_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=122642 ;

-- --------------------------------------------------------

--
-- Table structure for table `gcomments`
--

CREATE TABLE IF NOT EXISTS `gcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `gallery_id` int(10) unsigned NOT NULL default '0',
  `visibility` smallint(1) default '1',
  `title` char(100) default NULL,
  `content` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reply_to` int(10) NOT NULL default '-100',
  `comment_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm','suspended','oldinvis','delbyparentcomment','parentcommentcensored') NOT NULL default 'visible',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `gallery_id` (`gallery_id`),
  KEY `gcomments_reply_to_idx` (`reply_to`),
  KEY `gcomments_idx6` (`created`),
  KEY `comment_visibility` (`comment_visibility`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1387739 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(20) NOT NULL,
  `description` varchar(255) default NULL,
  `urlname` char(20) default NULL,
  `private` tinyint(1) default NULL,
  `created` datetime NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_memberships`
--

CREATE TABLE IF NOT EXISTS `group_memberships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `type` enum('owner','participant') default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_permissions`
--

CREATE TABLE IF NOT EXISTS `group_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `view` int(10) unsigned default NULL,
  `download` int(10) unsigned default NULL,
  `comment` int(10) unsigned default NULL,
  `hotlink` int(10) unsigned default NULL,
  `favorite` int(10) unsigned default NULL,
  `rate` int(10) unsigned default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `heartbeat`
--

CREATE TABLE IF NOT EXISTS `heartbeat` (
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ignored_users`
--

CREATE TABLE IF NOT EXISTS `ignored_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `blocker_id` int(11) default NULL,
  `expiration` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `reason` varchar(255) character set latin1 default NULL,
  PRIMARY KEY  (`id`),
  KEY `blocker_id` (`blocker_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6405 ;

-- --------------------------------------------------------

--
-- Table structure for table `karma_events`
--

CREATE TABLE IF NOT EXISTS `karma_events` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `effect` int(11) default NULL,
  `description` varchar(255) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `karma_ranks`
--

CREATE TABLE IF NOT EXISTS `karma_ranks` (
  `id` int(11) NOT NULL auto_increment,
  `rank` int(11) default NULL,
  `name` text collate latin1_general_ci,
  `canComment` int(11) default '1',
  `canUpload` int(11) default '1',
  `canTag` int(11) default '1',
  `rating_cap` int(11) NOT NULL default '100000',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `karma_ratings`
--

CREATE TABLE IF NOT EXISTS `karma_ratings` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `base` int(11) default '0',
  `project_rating` int(11) default '0',
  `gallery_rating` int(11) default '0',
  `comment_rating` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=74375 ;

-- --------------------------------------------------------

--
-- Table structure for table `karma_settings`
--

CREATE TABLE IF NOT EXISTS `karma_settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `value` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `lovers`
--

CREATE TABLE IF NOT EXISTS `lovers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `ipaddress` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`),
  KEY `ipaddress` (`ipaddress`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=642720 ;

-- --------------------------------------------------------

--
-- Table structure for table `mgallery_tags`
--

CREATE TABLE IF NOT EXISTS `mgallery_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=800 ;

-- --------------------------------------------------------

--
-- Table structure for table `mgcomments`
--

CREATE TABLE IF NOT EXISTS `mgcomments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5662 ;

-- --------------------------------------------------------

--
-- Table structure for table `mpcomments`
--

CREATE TABLE IF NOT EXISTS `mpcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `comment_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53241 ;

-- --------------------------------------------------------

--
-- Table structure for table `mproject_tags`
--

CREATE TABLE IF NOT EXISTS `mproject_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(10) NOT NULL auto_increment,
  `to_user_id` int(10) NOT NULL,
  `from_user_name` varchar(20) collate utf8_unicode_ci default NULL,
  `project_id` int(10) default NULL,
  `project_owner_name` varchar(20) collate utf8_unicode_ci default NULL,
  `gallery_id` int(10) default NULL,
  `comment_id` int(10) default NULL,
  `extra` text collate utf8_unicode_ci,
  `notification_type_id` int(10) NOT NULL,
  `status` enum('READ','UNREAD') collate utf8_unicode_ci NOT NULL default 'UNREAD',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `to_user_id` (`to_user_id`,`project_id`,`gallery_id`,`notification_type_id`,`status`,`created`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3840009 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_histories`
--

CREATE TABLE IF NOT EXISTS `notification_histories` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `ipaddress` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1946150 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_types`
--

CREATE TABLE IF NOT EXISTS `notification_types` (
  `id` int(10) NOT NULL auto_increment,
  `type` varchar(127) collate utf8_unicode_ci NOT NULL,
  `template` text collate utf8_unicode_ci NOT NULL,
  `is_admin` binary(1) NOT NULL default '0',
  `negative` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type_2` (`type`),
  KEY `is_admin` (`is_admin`),
  KEY `negative` (`negative`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Table structure for table `oldnotifications`
--

CREATE TABLE IF NOT EXISTS `oldnotifications` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `type` int(10) NOT NULL default '0',
  `custom_message` text collate latin1_general_ci NOT NULL,
  `status` enum('unread','read') collate latin1_general_ci NOT NULL default 'unread',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1413501 ;

-- --------------------------------------------------------

--
-- Table structure for table `pcomments`
--

CREATE TABLE IF NOT EXISTS `pcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `content` text,
  `visibility` smallint(1) default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `reply_to_id` int(11) default NULL,
  `created` datetime default NULL,
  `reply_to` int(11) default '-100',
  `comment_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm','suspended','oldinvis','delbyparentcomment','parentcommentcensored') NOT NULL default 'visible',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `visibility` (`visibility`),
  KEY `pcomments_reply_to_idx` (`reply_to`),
  KEY `pcomments_idx6` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4020447 ;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `permission_users`
--

CREATE TABLE IF NOT EXISTS `permission_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `description` text,
  `rating` float default NULL,
  `views` int(11) default NULL,
  `anonviews` int(11) default NULL,
  `num_favoriters` int(10) default NULL,
  `num_raters` int(10) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `loveit` int(10) unsigned default NULL,
  `loveitsuniqueip` int(10) unsigned default NULL,
  `num_bookmarks` int(255) NOT NULL,
  `version` smallint(255) unsigned NOT NULL default '1',
  `flagit` int(10) unsigned NOT NULL default '0',
  `numberOfSprites` int(10) unsigned default NULL,
  `totalScripts` int(10) unsigned default NULL,
  `has_sound_blocks` tinyint(3) unsigned default NULL,
  `has_sensorboard_blocks` tinyint(3) unsigned default NULL,
  `related_project_id` int(10) unsigned default NULL,
  `based_on_pid` int(10) default NULL,
  `root_based_on_pid` int(10) unsigned default NULL,
  `related_username` varchar(100) default NULL,
  `proj_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm') NOT NULL default 'visible',
  `vischangedbyid` int(10) default NULL,
  `vischangedtime` datetime default NULL,
  `safe` enum('high','mid','low') default 'low',
  `status` enum('notreviewed','censored','safe','notsafe') NOT NULL default 'notreviewed',
  `locked` int(11) NOT NULL,
  `remixes` int(11) default '0',
  `remixer` int(11) default '0',
  `upload_ip` bigint(20) default NULL,
  `country` char(2) default NULL,
  `scratch_version_date` date default NULL COMMENT 'added since version 1.3',
  PRIMARY KEY  (`id`),
  KEY `creator` (`user_id`),
  KEY `numsprites` (`numberOfSprites`),
  KEY `totalScripts` (`totalScripts`),
  KEY `related_project_id` (`related_project_id`),
  KEY `related_username` (`related_username`),
  KEY `created` (`created`),
  KEY `proj_visibility` (`proj_visibility`),
  KEY `views` (`views`),
  KEY `based_on_pid` (`based_on_pid`),
  KEY `root_based_on_pid` (`root_based_on_pid`),
  KEY `flagit` (`flagit`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1003798 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects_backup`
--

CREATE TABLE IF NOT EXISTS `projects_backup` (
  `id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(100) default NULL,
  `description` text,
  `rating` float default NULL,
  `views` int(11) default NULL,
  `num_favoriters` int(10) default NULL,
  `num_raters` int(10) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  `loveit` int(10) unsigned default NULL,
  `num_bookmarks` int(255) NOT NULL default '0',
  `version` smallint(255) unsigned NOT NULL default '1',
  `flagit` int(10) unsigned NOT NULL default '0',
  `numberOfSprites` int(10) unsigned default NULL,
  `totalScripts` int(10) unsigned default NULL,
  `related_project_id` int(10) unsigned default NULL,
  `related_username` varchar(255) default NULL,
  `proj_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm') NOT NULL default 'visible',
  `vischangedbyid` int(10) default NULL,
  `vischangedtime` datetime default NULL,
  `safe` enum('high','mid','low') default 'low',
  `status` enum('notreviewed','censored','safe','notsafe') NOT NULL default 'notreviewed',
  `locked` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_frontpage`
--

CREATE TABLE IF NOT EXISTS `projects_frontpage` (
  `id` int(10) NOT NULL auto_increment,
  `project_id` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('top_loved','top_viewed','top_downloaded','top_remixed','featured','surprise','feature_gallery','curator_favorites','design_studio') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`type`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15618 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_flags`
--

CREATE TABLE IF NOT EXISTS `project_flags` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `violent` tinyint(1) NOT NULL default '0',
  `obscene` tinyint(1) NOT NULL default '0',
  `disrespectful` tinyint(1) NOT NULL default '0',
  `m_obscene` tinyint(1) NOT NULL default '0',
  `m_violent` tinyint(1) NOT NULL default '0',
  `mature` tinyint(1) NOT NULL default '0',
  `admin_id` int(11) default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `feature_admin_id` int(11) default NULL,
  `feature_timestamp` timestamp NULL default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13157 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_permissions`
--

CREATE TABLE IF NOT EXISTS `project_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `view` int(10) unsigned default NULL,
  `download` int(10) unsigned default NULL,
  `comment` int(10) unsigned default NULL,
  `rate` int(10) unsigned default NULL,
  `favorite` int(10) unsigned default NULL,
  `hotlink` int(10) unsigned default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_saves`
--

CREATE TABLE IF NOT EXISTS `project_saves` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned default NULL,
  `user_id` int(10) unsigned default NULL,
  `related_project_name` varchar(255) default NULL,
  `related_username` varchar(255) default NULL,
  `related_savername` varchar(255) default NULL,
  `date` datetime NOT NULL,
  `related_saver_id` int(10) unsigned default NULL,
  `related_project_id` int(10) unsigned default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `identifier_set` USING BTREE (`date`,`project_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6865463 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_scripts`
--

CREATE TABLE IF NOT EXISTS `project_scripts` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `text_scripts` mediumtext,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `text_scripts` (`text_scripts`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1094759 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_shares`
--

CREATE TABLE IF NOT EXISTS `project_shares` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned default NULL,
  `user_id` int(10) unsigned default NULL,
  `related_project_name` varchar(255) default NULL,
  `related_username` varchar(255) default NULL,
  `related_savername` varchar(255) default NULL,
  `related_user_id` int(10) unsigned default NULL,
  `related_project_id` int(10) unsigned default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `identifier_set` USING BTREE (`date`,`project_id`,`user_id`),
  KEY `project_id` (`project_id`),
  KEY `related_project_id` (`related_project_id`),
  KEY `project_shares_rel_proj_id_idx` (`related_project_id`),
  KEY `project_shares_rel_user_id_idx` (`related_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2028918 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_tags`
--

CREATE TABLE IF NOT EXISTS `project_tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1803542 ;

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE IF NOT EXISTS `relationships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `relationship_type_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=674884 ;

-- --------------------------------------------------------

--
-- Table structure for table `relationship_types`
--

CREATE TABLE IF NOT EXISTS `relationship_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(20) NOT NULL,
  `description` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `remixed_projects`
--

CREATE TABLE IF NOT EXISTS `remixed_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `remix_id` int(10) unsigned default NULL,
  `oproject_id` int(10) unsigned default NULL,
  `remix_name` varchar(255) default NULL,
  `oproject_name` varchar(255) default NULL,
  `remix_desc` text,
  `oproject_desc` text,
  `remix_sprites` int(10) unsigned default NULL,
  `oproject_sprites` int(10) unsigned default NULL,
  `remix_scripts` int(10) unsigned default NULL,
  `oproject_scripts` int(10) unsigned default NULL,
  `remixer_id` int(10) unsigned default NULL,
  `ocreator_id` int(10) unsigned default NULL,
  `remixer_name` varchar(255) default NULL,
  `ocreator_name` varchar(255) default NULL,
  `remixer_gender` enum('female','male') default NULL,
  `ocreator_gender` enum('female','male') default NULL,
  `remixer_byear` int(10) unsigned default NULL,
  `ocreator_byear` int(10) unsigned default NULL,
  `remixer_bmonth` int(10) unsigned default NULL,
  `ocreator_bmonth` int(10) unsigned default NULL,
  `remixer2ocreator` text,
  `ocreator2remixer` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `remixes`
--

CREATE TABLE IF NOT EXISTS `remixes` (
  `id` int(11) NOT NULL auto_increment,
  `remix_project_id` int(11) NOT NULL,
  `original_project_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=341 ;

-- --------------------------------------------------------

--
-- Table structure for table `remix_history`
--

CREATE TABLE IF NOT EXISTS `remix_history` (
  `project_id` bigint(20) unsigned default NULL,
  `based_on` bigint(20) unsigned default NULL,
  `date` datetime default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `remix_history_allbeforejun7`
--

CREATE TABLE IF NOT EXISTS `remix_history_allbeforejun7` (
  `project_id` bigint(20) unsigned default NULL,
  `based_on` bigint(20) unsigned default NULL,
  `root_based_on` bigint(20) default NULL,
  `date` datetime default NULL,
  `ignore` tinyint(1) NOT NULL default '0',
  KEY `ignore` (`ignore`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `remix_notifications`
--

CREATE TABLE IF NOT EXISTS `remix_notifications` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `ntype` enum('positive','neutral','generosity','conformity','reputation','nonotification') NOT NULL default 'neutral',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=621 ;

-- --------------------------------------------------------

--
-- Table structure for table `shariables`
--

CREATE TABLE IF NOT EXISTS `shariables` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `type` char(100) NOT NULL,
  `name` char(100) NOT NULL,
  `value` text NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `sprites`
--

CREATE TABLE IF NOT EXISTS `sprites` (
  `id` int(11) NOT NULL auto_increment,
  `name` text character set latin1,
  `user_id` int(11) NOT NULL,
  `description` text character set latin1,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `isFeatured` int(11) default '0',
  `status` enum('banned','normal','notreviewed','safe') character set latin1 NOT NULL default 'notreviewed',
  `views` int(11) default '0',
  `downloads` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `sprite_comments`
--

CREATE TABLE IF NOT EXISTS `sprite_comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  `content` text character set latin1,
  `status` enum('bycomm','byadmin','normal') collate latin1_general_ci default 'normal',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sprite_contributors`
--

CREATE TABLE IF NOT EXISTS `sprite_contributors` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sprite_flags`
--

CREATE TABLE IF NOT EXISTS `sprite_flags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sprite_tags`
--

CREATE TABLE IF NOT EXISTS `sprite_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `taggers`
--

CREATE TABLE IF NOT EXISTS `taggers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `taggers_user_id_idx` (`user_id`),
  KEY `taggers_project_id_idx` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=96565 ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(100) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=233000 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_count`
--

CREATE TABLE IF NOT EXISTS `tag_count` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_flags`
--

CREATE TABLE IF NOT EXISTS `tag_flags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41758 ;

-- --------------------------------------------------------

--
-- Table structure for table `tcomments`
--

CREATE TABLE IF NOT EXISTS `tcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `theme_id` int(10) unsigned NOT NULL,
  `visibility` smallint(1) default '1',
  `title` char(100) default NULL,
  `content` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27516 ;

-- --------------------------------------------------------

--
-- Table structure for table `temp_remix_projects_flagged`
--

CREATE TABLE IF NOT EXISTS `temp_remix_projects_flagged` (
  `project_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `based_on_pid` int(11) default NULL,
  `reasons` varchar(500) default NULL,
  `flag_timestamp` datetime default NULL,
  `view_timestamp` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `thanks`
--

CREATE TABLE IF NOT EXISTS `thanks` (
  `id` int(11) NOT NULL auto_increment,
  `sender_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `reason` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ipaddress` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(100) default NULL,
  `description` text,
  `user_id` int(10) unsigned NOT NULL,
  `icon` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `total_projects` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5561 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_memberships`
--

CREATE TABLE IF NOT EXISTS `theme_memberships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `theme_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21744 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_projects`
--

CREATE TABLE IF NOT EXISTS `theme_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `theme_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49090 ;

-- --------------------------------------------------------

--
-- Table structure for table `theme_requests`
--

CREATE TABLE IF NOT EXISTS `theme_requests` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `to_id` int(10) NOT NULL,
  `theme_id` int(10) default NULL,
  `status` enum('pending','accepted','declined') default NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33366 ;

-- --------------------------------------------------------

--
-- Table structure for table `thumbnails`
--

CREATE TABLE IF NOT EXISTS `thumbnails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `data` blob,
  `type` enum('mini','medium') NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` char(20) NOT NULL,
  `villager` tinyint(1) NOT NULL,
  `firstname` char(25) NOT NULL,
  `lastname` char(25) NOT NULL,
  `gender` varchar(25) NOT NULL,
  `urlname` char(20) NOT NULL,
  `role` enum('user','admin') NOT NULL default 'user',
  `email` char(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `buddyicon` varchar(255) default NULL,
  `organization` char(255) default NULL,
  `city` char(255) default NULL,
  `created` datetime default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `state` char(255) default NULL,
  `country` char(255) default NULL,
  `hearabout` varchar(255) default NULL,
  `interestsabout` varchar(255) default NULL,
  `comments` varchar(255) default NULL,
  `bmonth` varchar(45) default NULL,
  `byear` varchar(45) default NULL,
  `notify_pcomment` tinyint(1) NOT NULL default '1',
  `notify_gcomment` tinyint(1) NOT NULL default '1',
  `ipaddress` bigint(20) NOT NULL,
  `status` enum('normal','locked','delbyadmin','delbyusr','blockedtemporarily') NOT NULL default 'normal',
  `blocked_till` timestamp NOT NULL default '0000-00-00 00:00:00',
  `userpicext` enum('pjpeg','jpg','jpeg','png','gif','x-png','bmp') default NULL,
  `userpic_suffix` char(30) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `urlname` (`urlname`),
  UNIQUE KEY `screenname` (`username`),
  KEY `email` (`email`),
  KEY `created` (`created`),
  KEY `status` (`status`),
  KEY `ipaddress` (`ipaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=496110 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_events`
--

CREATE TABLE IF NOT EXISTS `user_events` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `ipaddress` bigint(20) NOT NULL,
  `time` datetime NOT NULL,
  `event` enum('view_frontpage','view_channel','view_gallery','view_project','do_gallery_comment','do_project_comment','do_project_tag','do_project_upload','do_project_update','do_login','do_logout') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_stats`
--

CREATE TABLE IF NOT EXISTS `user_stats` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `lastin` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=186633 ;

-- --------------------------------------------------------

--
-- Table structure for table `view_stats`
--

CREATE TABLE IF NOT EXISTS `view_stats` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `ipaddress` bigint(20) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `ipaddress` (`ipaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11616626 ;

-- --------------------------------------------------------

--
-- Table structure for table `view_stats_20081211`
--

CREATE TABLE IF NOT EXISTS `view_stats_20081211` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `ipaddress` bigint(20) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `ipaddress` (`ipaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23870472 ;

-- --------------------------------------------------------

--
-- Table structure for table `view_stats_20090808`
--

CREATE TABLE IF NOT EXISTS `view_stats_20090808` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `ipaddress` bigint(20) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `ipaddress` (`ipaddress`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31756670 ;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `whitelisted_ip_addresses`
--

CREATE TABLE IF NOT EXISTS `whitelisted_ip_addresses` (
  `id` int(11) NOT NULL auto_increment,
  `ipaddress` bigint(20) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `email` char(100) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `comments` mediumtext NOT NULL,
  `no_of_student` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

