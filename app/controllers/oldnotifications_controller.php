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
		$user_id = $this->Session->read('User.id');
		if(empty($user_id)) {
			$this->cakeError('error404');
		}
		$user_record = $this->User->find("id = $user_id");
		if(empty($user_record)) {
			$this->cakeError('error404');
		}
		$notify_pcomment = $user_record['User']['notify_pcomment'];
		$notify_gcomment = $user_record['User']['notify_gcomment'];
		$this->set('notify_pcomment', $notify_pcomment);
		$this->set('notify_gcomment', $notify_gcomment);
		
    	$notifications = $this->Oldnotification->findAll("status='unread' AND user_id=$user_id", NULL, 'id DESC');
		$this->set('notifications',$notifications);
		$this->set('title', "Scratch | Messages and notifications");
		$this->render('oldnotifications');
	}
}
?>
