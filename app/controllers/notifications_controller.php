<?php
class NotificationsController extends AppController {
	var $name = 'Notifications';
	var $uses = array('Notification', 'FriendRequest');
	var $helpers = array('Ajax');
	
	/**
	* Called before every controller action
	* Overrides AppController::beforeFilter()
	*/
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
	function index() {
		$memcache = new Memcache;
		$memcache->connect('localhost', 11211) or die ("Could not connect");
		$prefix = MEMCACHE_PREFIX;
		
		$user_id = $this->Session->read('User.id');
		if(empty($user_id)) {
			$this->cakeError('error404');
		}
		
		$user_record = $this->User->find("id = $user_id");
		if(empty($user_record)) {
			$this->cakeError('error404');
		}
		
		$username = $user_record['User']['username'];
		$notify_pcomment = $user_record['User']['notify_pcomment'];
		$notify_gcomment = $user_record['User']['notify_gcomment'];
		$this->set('username', $username);
		$this->set('notify_pcomment', $notify_pcomment);
		$this->set('notify_gcomment', $notify_gcomment);
		

    	$notifications = $memcache->get("$prefix-notifications-$user_id");
		if ( $notifications == "" ) {
			$notifications = $this->Notification->getNotifications($user_id);
			$memcache->set("$prefix-notifications-$user_id", $notifications, false, 600) or die ("Failed to save data at the server");
		}
		$this->set('notifications', $notifications);
		
		$friend_requests = $memcache->get("$prefix-friend_requests-$user_id");
		if ( $friend_requests == "" ) {
			$friend_requests = $this->FriendRequest->findAll(array("to_id" => $user_id, "FriendRequest.status" => "pending"));
			$memcache->set("$prefix-friend_requests-$user_id", $friend_requests, false, 600) or die ("Failed to save data at the server");
		}
		$this->set('friend_requests', $friend_requests);
		$memcache->close();

		$this->set('title', "Scratch | Messages and notifications");
		$this->render('notifications');
	}
	
	/* 
	 * AJAX-called
	 * marks the indicated notification as read
	 */
	function hide($nid) {
		 $this->autoRender = false;
		 $id = $nid;
		 $this->Notification->id = $id;
		 $this->Notification->saveField('status','READ');
		 $this->render('hide_notification_ajax', 'ajax');
	}
	
	/* 
	 * AJAX-called
	 * marks all user notification as read
	 */
	function hide_all() {
		 $this->autoRender = false;
		 $user_id = $this->getLoggedInUserID();
		 //notification update
		 $this->Notification->updateAll(array('status' => '"READ"'), array('Notification.to_user_id' => $user_id));
		 //friend request update
		 $this->FriendRequest->updateAll(array('FriendRequest.status' => '"declined"'), array('to_id' => $user_id));
		 $this->render('hide_all_notification_ajax', 'ajax');
	}
	
	function pnotify() {
		 $this->autoRender = false;
		 $user_id = $this->Session->read('User.id');
		 $this->User->id = $user_id;
		 $this->User->saveField('notify_pcomment',1);
		 $user_record = $this->User->find("id = $user_id");
		 $notify_pcomment = $user_record['User']['notify_pcomment'];
		 $this->set('notify_pcomment', $notify_pcomment);
		 $this->render("pnotify_ajax", "ajax");	 
	}
	
	function unpnotify() {
		 $this->autoRender = false;
		 $user_id = $this->Session->read('User.id');
		 $this->User->id = $user_id;
		 $this->User->saveField('notify_pcomment',0);
		 $user_record = $this->User->find("id = $user_id");
		 $notify_pcomment = $user_record['User']['notify_pcomment'];
		 $this->set('notify_pcomment', $notify_pcomment);
		 $this->render("pnotify_ajax", "ajax");
	}
	
	function gnotify() {
		 $this->autoRender = false;
		 $user_id = $this->Session->read('User.id');
		 $this->User->id = $user_id;
		 $this->User->saveField('notify_gcomment',1);
		 $user_record = $this->User->find("id = $user_id");
		 $notify_gcomment = $user_record['User']['notify_gcomment'];
		 $this->set('notify_gcomment', $notify_gcomment);
		 $this->render("gnotify_ajax", "ajax");	 
	}
	
	function ungnotify() {
		 $this->autoRender = false;
		 $user_id = $this->Session->read('User.id');
		 $this->User->id = $user_id;
		 $this->User->saveField('notify_gcomment',0);
		 $user_record = $this->User->find("id = $user_id");
		 $notify_gcomment = $user_record['User']['notify_gcomment'];
		 $this->set('notify_gcomment', $notify_gcomment);
		 $this->render("gnotify_ajax", "ajax");
	}
}
?>