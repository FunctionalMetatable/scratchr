<?php
class OldnotificationsController extends AppController {
   var $name = 'Oldnotifications';
   
	/**
	* Called before every controller action
	 * Overrides AppController::beforeFilter()
	*/
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
    /*
	* shows notifications to user
	*/
	function index() {
		$memcache = new Memcache;
		$memcache->connect('localhost', 11211) or die ("Could not connect");
		$prefix = MEMCACHE_PREFIX;
		$user_id = $this->Session->read('User.id');
		if(empty($user_id))
		$this->cakeError('error404');
		$user_record = $this->User->find("id = $user_id");
		if(empty($user_record))
		$this->cakeError('error404');
		$notify_pcomment = $user_record['User']['notify_pcomment'];
		$notify_gcomment = $user_record['User']['notify_gcomment'];
		$this->set('notify_pcomment', $notify_pcomment);
		$this->set('notify_gcomment', $notify_gcomment);
		

    	$notifications = $memcache->get("$prefix-oldnotifications-$user_id");
		if ( $notifications == "" ) {
			$notifications_tmp = $this->Oldnotification->findAll("status='unread' AND user_id=$user_id", NULL, 'id DESC');
			$memcache->set("$prefix-oldnotifications-$user_id", $notifications_tmp, false, 600) or die ("Failed to save data at the server");
			$this->set('notifications',$notifications_tmp);
		} else {
			$this->set('notifications',$notifications);
		}

    	$memcache->close();

		$this->set('title', "Scratch | Messages and notifications");
		$this->render('oldnotifications');
	}
}
?>
