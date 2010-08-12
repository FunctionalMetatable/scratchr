CREATE TABLE `experimental_cancel_logs` (
    `id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int( 10 ) unsigned NOT NULL,
    `cancelled_action` char(20) NOT NULL,
    `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
    `ipaddress` bigint(20) default NULL,
    PRIMARY KEY ( `id` ) ,
    KEY `user_id` ( `user_id` )
);
