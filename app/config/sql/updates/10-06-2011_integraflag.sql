CREATE TABLE `integraflags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `flagger_ids` varchar(255) NOT NULL,
  `flagged_id` int(15) NOT NULL,
  `project_id` int(10) DEFAULT NULL,
  `gallery_id` int(10) DEFAULT NULL,
  `flag_message` text,
  `flagged_content` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('open','closed','review') NOT NULL DEFAULT 'open',
  `handled_by` int(15) DEFAULT NULL,
  `action` text NOT NULL,
  `notification_id` int(15) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
);
