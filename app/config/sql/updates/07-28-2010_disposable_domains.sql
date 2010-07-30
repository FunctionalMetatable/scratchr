--
-- Table structure for table `disposable_domains`
--

CREATE TABLE IF NOT EXISTS `disposable_domains` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `tld` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tld` (`tld`)
);

--
-- Dumping data for table `disposable_domains`
--

INSERT INTO `disposable_domains` (`id`, `tld`) VALUES
(1, 'mailinator.com'),
(2, 'mytrashmail.com'),
(3, 'mailexpire.com'),
(4, 'temporaryinbox.com'),
(5, 'maileater.com'),
(6, 'jetable.org'),
(7, 'spambox.us'),
(8, 'guerillamail.com'),
(9, 'spamhole.com'),
(10, '10minutemail.com'),
(11, 'dontreg.com'),
(12, 'tempomail.fr'),
(13, 'tempemail.net'),
(14, 'pookmail.com'),
(15, 'spamfree24.org'),
(16, 'kasmail.com'),
(17, 'spammotel.com'),
(18, 'greensloth.com'),
(19, 'spamspot.com'),
(20, 'spam.la');
