<?php
/*
 * UserEvent model uses Amazon SimpleDB
 * We need to cretae a domain named user_events in SimpleDB first
 * $sdb->create_domain('user_events');
 */
class UserEvent extends AppModel {

	var $name = 'UserEvent';
    var $useTable = null;
    var $domainName = 'user_events';
    var $sdb = null;
    var $handles;

    function __construct() {
        App::import('Vendor', 'Tarzan', array('file' => 'tarzan'.DS.'tarzan.class.php'));
        $this->sdb = new AmazonSDB();
        $this->handles = array();
    }
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
        
        $attrs = array( 'user_id' => $user_id, 'ipaddress' => $ip,
                     'time' => $time, 'event' => $event);

        $this->handles[] = $this->sdb->put_attributes($this->domainName,
                        String::uuid(),
                        $attrs, null, true);

        register_shutdown_function(array(&$this, 'sendMultiRequest'));
    }

    function find($user_id) {
        $expression = "select * from {$this->domainName} where user_id = '{$user_id}'";
        return $this->sdb->select($expression);
    }


    function sendMultiRequest() {
        @ob_flush();
        $request = new TarzanHTTPRequest(null);
        $request->sendMultiRequest($this->handles);
    }
}
?>