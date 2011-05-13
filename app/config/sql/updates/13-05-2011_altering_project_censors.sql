ALTER TABLE  `projects` CHANGE  `proj_visibility`  `proj_visibility` ENUM('visible', 'delbyusr', 'delbyadmin', 'censbyadmin', 'censbycomm', 'censbycm' ) NOT NULL DEFAULT  'visible';
