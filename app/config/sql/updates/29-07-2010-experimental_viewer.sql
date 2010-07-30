CREATE TABLE `beta_users` (
    `id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT ,
    `user_id` int( 10 ) unsigned NOT NULL,
    `opt_in_stamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
    `opt_out_stamp` timestamp,
    `enabled` boolean NOT NULL default FALSE,
    PRIMARY KEY ( `id` ) ,
    KEY `user_id` ( `user_id` )
);

CREATE TABLE `beta_views` (
    `id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int( 10 ) unsigned NOT NULL,
    `project_id` int( 10 ) unsigned NOT NULL,
    `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY ( `id` ) ,
    KEY `user_id` ( `user_id` ),
    KEY `project_id` ( `project_id` )
);

