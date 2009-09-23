 ALTER TABLE `gcomments` CHANGE `comment_visibility` `comment_visibility` ENUM( 'visible', 'delbyusr', 'delbyadmin', 'censbyadmin', 'censbycomm', 'suspended', 'oldinvis', 'delbyparentcomment' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'visible' 

ALTER TABLE `pcomments` CHANGE `comment_visibility` `comment_visibility` ENUM( 'visible', 'delbyusr', 'delbyadmin', 'censbyadmin', 'censbycomm', 'suspended', 'oldinvis', 'delbyparentcomment' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'visible'


 ALTER TABLE `pcomments` CHANGE `comment_visibility` `comment_visibility` ENUM( 'visible', 'delbyusr', 'delbyadmin', 'censbyadmin', 'censbycomm', 'suspended', 'oldinvis', 'delbyparentcomment', 'parentcommentcensored' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'visible';
 ALTER TABLE `gcomments` CHANGE `comment_visibility` `comment_visibility` ENUM( 'visible', 'delbyusr', 'delbyadmin', 'censbyadmin', 'censbycomm', 'suspended', 'oldinvis', 'delbyparentcomment', 'parentcommentcensored' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'visible'  