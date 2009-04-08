-- MySQL dump 10.11
--
-- Host: scratchdb    Database: beta
-- ------------------------------------------------------
-- Server version	5.0.45-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_comments`
--

DROP TABLE IF EXISTS `admin_comments`;
CREATE TABLE `admin_comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `content` text character set latin1 NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `admin_tags`
--

DROP TABLE IF EXISTS `admin_tags`;
CREATE TABLE `admin_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `tag_id` int(11) default NULL,
  `status` enum('active','inactive') character set latin1 default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `anant_test`
--

DROP TABLE IF EXISTS `anant_test`;
CREATE TABLE `anant_test` (
  `anant1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `content` text character set latin1,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `isOn` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `apcomments`
--

DROP TABLE IF EXISTS `apcomments`;
CREATE TABLE `apcomments` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Table structure for table `blocked_ips`
--

DROP TABLE IF EXISTS `blocked_ips`;
CREATE TABLE `blocked_ips` (
  `id` int(11) NOT NULL auto_increment,
  `ip` bigint(20) default '0',
  `user_id` int(11) default '0',
  `reason` text collate latin1_general_ci,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `blocked_users`
--

DROP TABLE IF EXISTS `blocked_users`;
CREATE TABLE `blocked_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `admin_id` int(11) default NULL,
  `reason` text collate latin1_general_ci,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=655 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `bookmarks`
--

DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cake_sessions`
--

DROP TABLE IF EXISTS `cake_sessions`;
CREATE TABLE `cake_sessions` (
  `id` varchar(255) NOT NULL default '',
  `data` varchar(1000) default NULL,
  `expires` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

--
-- Table structure for table `clubbed_galleries`
--

DROP TABLE IF EXISTS `clubbed_galleries`;
CREATE TABLE `clubbed_galleries` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL default '0',
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Table structure for table `clubbed_galleries_copy`
--

DROP TABLE IF EXISTS `clubbed_galleries_copy`;
CREATE TABLE `clubbed_galleries_copy` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL default '0',
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Table structure for table `clubbed_themes`
--

DROP TABLE IF EXISTS `clubbed_themes`;
CREATE TABLE `clubbed_themes` (
  `id` int(10) NOT NULL auto_increment,
  `theme_id` int(10) NOT NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Table structure for table `curators`
--

DROP TABLE IF EXISTS `curators`;
CREATE TABLE `curators` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Table structure for table `downloaders`
--

DROP TABLE IF EXISTS `downloaders`;
CREATE TABLE `downloaders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `time` (`timestamp`),
  KEY `downloaders_user_id_idx` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=808977 DEFAULT CHARSET=latin1;

--
-- Table structure for table `downloaders_copy`
--

DROP TABLE IF EXISTS `downloaders_copy`;
CREATE TABLE `downloaders_copy` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `time` (`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=144371 DEFAULT CHARSET=latin1;

--
-- Table structure for table `dusers`
--

DROP TABLE IF EXISTS `dusers`;
CREATE TABLE `dusers` (
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
) ENGINE=InnoDB AUTO_INCREMENT=711771 DEFAULT CHARSET=latin1;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=215178 DEFAULT CHARSET=latin1;

--
-- Table structure for table `featured_galleries`
--

DROP TABLE IF EXISTS `featured_galleries`;
CREATE TABLE `featured_galleries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `gallery_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `theme_id` (`gallery_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=latin1;

--
-- Table structure for table `featured_projects`
--

DROP TABLE IF EXISTS `featured_projects`;
CREATE TABLE `featured_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=857 DEFAULT CHARSET=latin1;

--
-- Table structure for table `featured_projects_backup`
--

DROP TABLE IF EXISTS `featured_projects_backup`;
CREATE TABLE `featured_projects_backup` (
  `id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `featured_themes`
--

DROP TABLE IF EXISTS `featured_themes`;
CREATE TABLE `featured_themes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `theme_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `theme_id` (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Table structure for table `flaggers`
--

DROP TABLE IF EXISTS `flaggers`;
CREATE TABLE `flaggers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `reasons` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15042 DEFAULT CHARSET=latin1;

--
-- Table structure for table `friend_requests`
--

DROP TABLE IF EXISTS `friend_requests`;
CREATE TABLE `friend_requests` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `to_id` int(10) NOT NULL,
  `status` enum('pending','accepted','declined') default NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `friend_request_user_id_idx` (`user_id`),
  KEY `friend_request_to_id` (`to_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=338419 DEFAULT CHARSET=latin1;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
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
) ENGINE=InnoDB AUTO_INCREMENT=44925 DEFAULT CHARSET=latin1;

--
-- Table structure for table `galleries_frontpage`
--

DROP TABLE IF EXISTS `galleries_frontpage`;
CREATE TABLE `galleries_frontpage` (
  `id` int(10) NOT NULL auto_increment,
  `gallery_id` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('featured') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gallery_id_2` (`gallery_id`,`type`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `gallery_flags`
--

DROP TABLE IF EXISTS `gallery_flags`;
CREATE TABLE `gallery_flags` (
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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `gallery_memberships`
--

DROP TABLE IF EXISTS `gallery_memberships`;
CREATE TABLE `gallery_memberships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `gallery_id` int(10) unsigned NOT NULL default '0',
  `type` int(10) NOT NULL default '2',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `rank` enum('owner','curator','member','bookmarker','contributor') NOT NULL default 'bookmarker',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=128510 DEFAULT CHARSET=latin1;

--
-- Table structure for table `gallery_projects`
--

DROP TABLE IF EXISTS `gallery_projects`;
CREATE TABLE `gallery_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `gallery_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `galllery_id_project_id` (`gallery_id`,`project_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=437056 DEFAULT CHARSET=latin1;

--
-- Table structure for table `gallery_requests`
--

DROP TABLE IF EXISTS `gallery_requests`;
CREATE TABLE `gallery_requests` (
  `id` int(10) NOT NULL auto_increment,
  `subscriber_id` int(10) NOT NULL default '0',
  `owner_id` int(10) NOT NULL default '0',
  `gallery_id` int(10) default NULL,
  `status` enum('pending','accepted','declined') default NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33366 DEFAULT CHARSET=latin1;

--
-- Table structure for table `gallery_tags`
--

DROP TABLE IF EXISTS `gallery_tags`;
CREATE TABLE `gallery_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41168 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `gcomments`
--

DROP TABLE IF EXISTS `gcomments`;
CREATE TABLE `gcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `gallery_id` int(10) unsigned NOT NULL default '0',
  `visibility` smallint(1) default '1',
  `title` char(100) default NULL,
  `content` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reply_to` int(10) NOT NULL default '-100',
  `comment_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm','suspended','oldinvis') NOT NULL default 'visible',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `gallery_id` (`gallery_id`),
  KEY `gcomments_reply_to_idx` (`reply_to`)
) ENGINE=InnoDB AUTO_INCREMENT=532240 DEFAULT CHARSET=latin1;

--
-- Table structure for table `group_memberships`
--

DROP TABLE IF EXISTS `group_memberships`;
CREATE TABLE `group_memberships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `type` enum('owner','participant') default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `group_permissions`
--

DROP TABLE IF EXISTS `group_permissions`;
CREATE TABLE `group_permissions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(20) NOT NULL,
  `description` varchar(255) default NULL,
  `urlname` char(20) default NULL,
  `private` tinyint(1) default NULL,
  `created` datetime NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `heartbeat`
--

DROP TABLE IF EXISTS `heartbeat`;
CREATE TABLE `heartbeat` (
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `ignored_users`
--

DROP TABLE IF EXISTS `ignored_users`;
CREATE TABLE `ignored_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `blocker_id` int(11) default NULL,
  `expiration` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `reason` varchar(255) character set latin1 default NULL,
  PRIMARY KEY  (`id`),
  KEY `blocker_id` (`blocker_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2822 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `karma_events`
--

DROP TABLE IF EXISTS `karma_events`;
CREATE TABLE `karma_events` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `effect` int(11) default NULL,
  `description` varchar(255) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `karma_ranks`
--

DROP TABLE IF EXISTS `karma_ranks`;
CREATE TABLE `karma_ranks` (
  `id` int(11) NOT NULL auto_increment,
  `rank` int(11) default NULL,
  `name` text collate latin1_general_ci,
  `canComment` int(11) default '1',
  `canUpload` int(11) default '1',
  `canTag` int(11) default '1',
  `rating_cap` int(11) NOT NULL default '100000',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `karma_ratings`
--

DROP TABLE IF EXISTS `karma_ratings`;
CREATE TABLE `karma_ratings` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `base` int(11) default '0',
  `project_rating` int(11) default '0',
  `gallery_rating` int(11) default '0',
  `comment_rating` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74375 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `karma_settings`
--

DROP TABLE IF EXISTS `karma_settings`;
CREATE TABLE `karma_settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate latin1_general_ci default NULL,
  `value` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `lovers`
--

DROP TABLE IF EXISTS `lovers`;
CREATE TABLE `lovers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=316593 DEFAULT CHARSET=latin1;

--
-- Table structure for table `mgallery_tags`
--

DROP TABLE IF EXISTS `mgallery_tags`;
CREATE TABLE `mgallery_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=800 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `mgcomments`
--

DROP TABLE IF EXISTS `mgcomments`;
CREATE TABLE `mgcomments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3813 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `mpcomments`
--

DROP TABLE IF EXISTS `mpcomments`;
CREATE TABLE `mpcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `comment_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30382 DEFAULT CHARSET=latin1;

--
-- Table structure for table `mproject_tags`
--

DROP TABLE IF EXISTS `mproject_tags`;
CREATE TABLE `mproject_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `notification_types`
--

DROP TABLE IF EXISTS `notification_types`;
CREATE TABLE `notification_types` (
  `id` int(10) NOT NULL auto_increment,
  `type` varchar(127) collate utf8_unicode_ci NOT NULL,
  `template` text collate utf8_unicode_ci NOT NULL,
  `is_admin` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type_2` (`type`),
  KEY `is_admin` (`is_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(10) NOT NULL auto_increment,
  `to_user_id` int(10) NOT NULL,
  `from_user_name` varchar(20) collate utf8_unicode_ci default NULL,
  `project_id` int(10) default NULL,
  `project_owner_name` varchar(20) collate utf8_unicode_ci default NULL,
  `gallery_id` int(10) default NULL,
  `extra` text collate utf8_unicode_ci,
  `notification_type_id` int(10) NOT NULL,
  `status` enum('READ','UNREAD') collate utf8_unicode_ci NOT NULL default 'UNREAD',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `to_user_id` (`to_user_id`,`project_id`,`gallery_id`,`notification_type_id`,`status`,`created`)
) ENGINE=InnoDB AUTO_INCREMENT=570320 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `oldnotifications`
--

DROP TABLE IF EXISTS `oldnotifications`;
CREATE TABLE `oldnotifications` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `type` int(10) NOT NULL default '0',
  `custom_message` text collate latin1_general_ci NOT NULL,
  `status` enum('unread','read') collate latin1_general_ci NOT NULL default 'unread',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1413501 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `pcomments`
--

DROP TABLE IF EXISTS `pcomments`;
CREATE TABLE `pcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `content` text,
  `visibility` smallint(1) default '1',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `reply_to_id` int(11) default NULL,
  `created` datetime default NULL,
  `reply_to` int(11) default '-100',
  `comment_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm','suspended','oldinvis') NOT NULL default 'visible',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `visibility` (`visibility`),
  KEY `pcomments_reply_to_idx` (`reply_to`)
) ENGINE=InnoDB AUTO_INCREMENT=1725205 DEFAULT CHARSET=latin1;

--
-- Table structure for table `permission_users`
--

DROP TABLE IF EXISTS `permission_users`;
CREATE TABLE `permission_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `url_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_flags`
--

DROP TABLE IF EXISTS `project_flags`;
CREATE TABLE `project_flags` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6679 DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_permissions`
--

DROP TABLE IF EXISTS `project_permissions`;
CREATE TABLE `project_permissions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_saves`
--

DROP TABLE IF EXISTS `project_saves`;
CREATE TABLE `project_saves` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3370899 DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_scripts`
--

DROP TABLE IF EXISTS `project_scripts`;
CREATE TABLE `project_scripts` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `text_scripts` mediumtext,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `text_scripts` (`text_scripts`)
) ENGINE=MyISAM AUTO_INCREMENT=459037 DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_shares`
--

DROP TABLE IF EXISTS `project_shares`;
CREATE TABLE `project_shares` (
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
) ENGINE=InnoDB AUTO_INCREMENT=951377 DEFAULT CHARSET=latin1;

--
-- Table structure for table `project_tags`
--

DROP TABLE IF EXISTS `project_tags`;
CREATE TABLE `project_tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=904375 DEFAULT CHARSET=latin1;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) default NULL,
  `description` text,
  `rating` float default NULL,
  `views` int(11) default NULL,
  `num_favoriters` int(10) default NULL,
  `num_raters` int(10) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `loveit` int(10) unsigned default NULL,
  `num_bookmarks` int(255) NOT NULL,
  `version` smallint(255) unsigned NOT NULL default '1',
  `flagit` int(10) unsigned NOT NULL default '0',
  `numberOfSprites` int(10) unsigned default NULL,
  `totalScripts` int(10) unsigned default NULL,
  `text_scripts` mediumtext,
  `has_sound_blocks` tinyint(3) unsigned default NULL,
  `has_sensorboard_blocks` tinyint(3) unsigned default NULL,
  `related_project_id` int(10) unsigned default NULL,
  `related_username` varchar(255) default NULL,
  `proj_visibility` enum('visible','delbyusr','delbyadmin','censbyadmin','censbycomm') NOT NULL default 'visible',
  `vischangedbyid` int(10) default NULL,
  `vischangedtime` datetime default NULL,
  `safe` enum('high','mid','low') default 'low',
  `status` enum('notreviewed','censored','safe','notsafe') NOT NULL default 'notreviewed',
  `locked` int(11) NOT NULL,
  `remixes` int(11) default '0',
  `remixer` int(11) default '0',
  `upload_ip` int(30) default NULL,
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
  FULLTEXT KEY `text_scripts` (`text_scripts`)
) ENGINE=MyISAM AUTO_INCREMENT=481819 DEFAULT CHARSET=latin1;

--
-- Table structure for table `projects_backup`
--

DROP TABLE IF EXISTS `projects_backup`;
CREATE TABLE `projects_backup` (
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

--
-- Table structure for table `projects_frontpage`
--

DROP TABLE IF EXISTS `projects_frontpage`;
CREATE TABLE `projects_frontpage` (
  `id` int(10) NOT NULL auto_increment,
  `project_id` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('top_loved','top_viewed','top_downloaded','top_remixed','featured','surprise','feature_gallery') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id_2` (`project_id`,`type`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5173 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `relationship_types`
--

DROP TABLE IF EXISTS `relationship_types`;
CREATE TABLE `relationship_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(20) NOT NULL,
  `description` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `relationships`
--

DROP TABLE IF EXISTS `relationships`;
CREATE TABLE `relationships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `relationship_type_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB AUTO_INCREMENT=348379 DEFAULT CHARSET=latin1;

--
-- Table structure for table `remixed_projects`
--

DROP TABLE IF EXISTS `remixed_projects`;
CREATE TABLE `remixed_projects` (
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
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

--
-- Table structure for table `shariables`
--

DROP TABLE IF EXISTS `shariables`;
CREATE TABLE `shariables` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `type` char(100) NOT NULL,
  `name` char(100) NOT NULL,
  `value` text NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

--
-- Table structure for table `sprite_comments`
--

DROP TABLE IF EXISTS `sprite_comments`;
CREATE TABLE `sprite_comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  `content` text character set latin1,
  `status` enum('bycomm','byadmin','normal') collate latin1_general_ci default 'normal',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `sprite_contributors`
--

DROP TABLE IF EXISTS `sprite_contributors`;
CREATE TABLE `sprite_contributors` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `sprite_flags`
--

DROP TABLE IF EXISTS `sprite_flags`;
CREATE TABLE `sprite_flags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `sprite_tags`
--

DROP TABLE IF EXISTS `sprite_tags`;
CREATE TABLE `sprite_tags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `sprite_id` int(11) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `sprites`
--

DROP TABLE IF EXISTS `sprites`;
CREATE TABLE `sprites` (
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Table structure for table `tag_count`
--

DROP TABLE IF EXISTS `tag_count`;
CREATE TABLE `tag_count` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tag_flags`
--

DROP TABLE IF EXISTS `tag_flags`;
CREATE TABLE `tag_flags` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14990 DEFAULT CHARSET=latin1;

--
-- Table structure for table `taggers`
--

DROP TABLE IF EXISTS `taggers`;
CREATE TABLE `taggers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `taggers_user_id_idx` (`user_id`),
  KEY `taggers_project_id_idx` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=96565 DEFAULT CHARSET=latin1;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(100) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=128193 DEFAULT CHARSET=latin1;

--
-- Table structure for table `tcomments`
--

DROP TABLE IF EXISTS `tcomments`;
CREATE TABLE `tcomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `theme_id` int(10) unsigned NOT NULL,
  `visibility` smallint(1) default '1',
  `title` char(100) default NULL,
  `content` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27516 DEFAULT CHARSET=latin1;

--
-- Table structure for table `thanks`
--

DROP TABLE IF EXISTS `thanks`;
CREATE TABLE `thanks` (
  `id` int(11) NOT NULL auto_increment,
  `sender_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `reason` text,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ipaddress` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `theme_memberships`
--

DROP TABLE IF EXISTS `theme_memberships`;
CREATE TABLE `theme_memberships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `theme_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21744 DEFAULT CHARSET=latin1;

--
-- Table structure for table `theme_projects`
--

DROP TABLE IF EXISTS `theme_projects`;
CREATE TABLE `theme_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `theme_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49090 DEFAULT CHARSET=latin1;

--
-- Table structure for table `theme_requests`
--

DROP TABLE IF EXISTS `theme_requests`;
CREATE TABLE `theme_requests` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `to_id` int(10) NOT NULL,
  `theme_id` int(10) default NULL,
  `status` enum('pending','accepted','declined') default NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33366 DEFAULT CHARSET=latin1;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
CREATE TABLE `themes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(100) default NULL,
  `description` text,
  `user_id` int(10) unsigned NOT NULL,
  `icon` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `total_projects` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5561 DEFAULT CHARSET=latin1;

--
-- Table structure for table `thumbnails`
--

DROP TABLE IF EXISTS `thumbnails`;
CREATE TABLE `thumbnails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `data` blob,
  `type` enum('mini','medium') NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_stats`
--

DROP TABLE IF EXISTS `user_stats`;
CREATE TABLE `user_stats` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `lastin` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101655 DEFAULT CHARSET=latin1;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
  UNIQUE KEY `screenname` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=270733 DEFAULT CHARSET=latin1;

--
-- Table structure for table `view_stats`
--

DROP TABLE IF EXISTS `view_stats`;
CREATE TABLE `view_stats` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `ipaddress` bigint(20) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `ipaddress` (`ipaddress`)
) ENGINE=MyISAM AUTO_INCREMENT=27197956 DEFAULT CHARSET=latin1;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
CREATE TABLE `votes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `whitelisted_ip_addresses`
--

DROP TABLE IF EXISTS `whitelisted_ip_addresses`;
CREATE TABLE `whitelisted_ip_addresses` (
  `id` int(11) NOT NULL auto_increment,
  `ipaddress` int(11) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `email` char(100) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `comments` mediumtext NOT NULL,
  `no_of_student` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-04-08 14:16:02
