CREATE TABLE IF NOT EXISTS `elections` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `candidate1` int(5) NOT NULL,
  `candidate2` int(5) NOT NULL,
  `candidate3` int(5) NOT NULL,
  `candidate4` int(5) NOT NULL,
  `candidate5` int(5) NOT NULL,
  `candidate6` int(5) NOT NULL,
  `candidate7` int(5) NOT NULL,
  `candidate8` int(5) NOT NULL,
  PRIMARY KEY (`id`)
);

