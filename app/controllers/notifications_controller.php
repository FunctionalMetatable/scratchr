<?php
class NotificationsController extends AppController {
	
	var $name = 'Notifications';
	var $uses = array('Notification', 'FriendRequest');
	var $helpers = array('Ajax', 'Pagination', 'Template');
	var $components = array('Pagination');

	/**
	* Called before every controller action
	* Overrides AppController::beforeFilter()
	*/
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
	function index() {
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
		
		$options = array( 'show'=>25  );
		$this->Pagination->ajaxAutoDetect = false;
		list($order, $limit, $page) = $this->Pagination->init(null, null, $options,
												$this->Notification->countAll($user_id));
		$notifications = $this->Notification->getNotifications($user_id, $page, $limit);
		$this->set('notifications', $notifications);
        $this->set('encoded_user_id', $this->encode($user_id));
		$this->set('title', "Scratch | Messages and notifications");
		$this->render('notifications');
	}
	
	/* 
	 * AJAX-called
	 * marks the indicated notification as read
	 */
	function hide($nid) {
		 $this->autoRender = false;
		 $user_id = $this->getLoggedInUserID();
		 $this->Notification->recursive = -1;
		 $notification = $this->Notification->find('first', array('fields' => array('to_user_id'),
											'conditions' => array('id' => $nid)));
		 if(empty($notification)) {
			$this->cakeError('error404');
		 }
		 if($notification['Notification']['to_user_id'] != $user_id) {
			$this->cakeError('error404');
		 }
		 
		 $this->Notification->id = $nid;
		 $this->Notification->saveField('status','READ');
		 $this->Notification->clear_memcached_notifications($user_id);
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
		 $this->Notification->clear_memcached_notifications($user_id);
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