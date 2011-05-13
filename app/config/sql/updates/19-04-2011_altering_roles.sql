ALTER TABLE  `users` CHANGE  `role`  `role` ENUM('user',  'admin',  'cm') NOT NULL DEFAULT  'user';
