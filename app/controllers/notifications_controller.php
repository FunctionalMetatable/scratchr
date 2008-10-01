<?php
class NotificationsController extends AppController {
   var $name = 'Notifications';
   var $components = array('PaginationSecondary', 'Pagination','RequestHandler','FileUploader');
   var $uses = array('Notification', 'FriendRequest');
   var $helpers = array('PaginationSecondary', 'Pagination','Ajax','Javascript');

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
		$user_id = $this->Session->read('User.id');
		$notifications = $this->Notification->findAll("status='unread' AND user_id=$user_id", NULL, 'id DESC');
		$friend_requests = $this->FriendRequest->findAll(array("to_id"=>$user_id, "FriendRequest.status"=>"pending"));
		$user_record = $this->User->find("id = $user_id");
		$notify_pcomment = $user_record['User']['notify_pcomment'];
		$notify_gcomment = $user_record['User']['notify_gcomment'];
		
		$this->set('title', "Scratch | Messages and notifications");
		$this->set('friend_requests', $friend_requests);
		$this->set('notifications',$notifications);
		$this->set('notify_pcomment', $notify_pcomment);
		$this->set('notify_gcomment', $notify_gcomment);
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
		 $this->Notification->saveField('status','read');
		 $this->render('hide_notification_ajax', 'ajax');
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
