 ALTER TABLE `whitelisted_ip_addresses` CHANGE `contact_name` `contact_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `email` `email` CHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `school_name` `school_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `comments` `comments` MEDIUMTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `no_of_student` `no_of_student` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL 