<?php
class UserEvent extends AppModel {

	var $name = 'UserEvent';

    /*
     * records user event in database
     * event can be one of the followings -
     * 'view_frontpage', 'view_channel', 'view_gallery', 'view_project',
     * 'do_gallery_comment', 'do_project_comment', 'do_project_tag',
     * 'do_project_upload', 'do_project_update', 'do_login'
     */
    function record($user_id, $ip, $event) {
        if(empty($user_id) || empty($ip) || empty($event)) { 
            return false;
        }

        //get the time
        $time = date("Y-m-d G:i:s");

        $sql = "INSERT INTO `user_events`"
            ." (`id`, `user_id`, `ipaddress`, `time`, `event`)"
            ." VALUES ("
            ." NULL , '$user_id', INET_ATON( '$ip' ) , '$time', '$event'"
            ." )";
        
        $this->query($sql);
    }
}
?>