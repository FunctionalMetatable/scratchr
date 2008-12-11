<?php
Class RequestsController extends AppController {
    var $helpers = null;
    var $uses = array('User', 'GalleryRequest', 'FriendRequest', 'GalleryMembership', 'Relationship', 'RelationshipType', 'Notification');
	var $request_type = Array('join', 'friend');
	var $components = Array('RequestHandler');
	
	function beforeFilter()
	{
		if (!$this->RequestHandler->isAjax())
			die;
	}
	
	function add()
	{
		exit;
	}
	
	function remove()
	{
		exit;
	}
	
	function accept()
	{
		$type = $this->params['url']['type'];
		if (!in_array($type, $this->request_type))
			exit;
		
		$session_UID = $this->getLoggedInUserID();
		if (!$session_UID)
			exit;
			
		switch($type)
		{
			case 'join':
			
			$theme_request_id = $this->params['url']['rid'];
			$this->ThemeRequest->id = $theme_request_id;
			$tr = $this->ThemeRequest->read();
			$theme_id = $tr['ThemeRequest']['theme_id'];
			$to_id = $tr['ThemeRequest']['to_id'];
			
			if ($to_id !== $session_UID)
				exit;
			
			if ($this->ThemeRequest->del())
			{
				$this->ThemeMembership->create();
				$this->ThemeMembership->save(array("ThemeMembership"=>array("user_id"=>$session_UID, "theme_id"=>$theme_id)));
				___("now you are a member");
			}
			break;			
				
			case 'friend':
		
			$friend_request_id = $this->params['url']['rid'];
			$this->FriendRequest->id = $friend_request_id;
			$fr = $this->FriendRequest->read();
			$to_id = $fr['FriendRequest']['to_id'];
			$friend_id = $fr['FriendRequest']['user_id'];		
			$relType = $this->RelationshipType->find("name = 'friend'"); 			

			if ($to_id !== $session_UID)
				exit;
				
			if (!empty($relType)) 
			{
				if ($this->FriendRequest->del())
				{
					$relTypeID = $relType['RelationshipType']['id'];
					$this->Relationship->create();
					$this->Relationship->save(Array("Relationship"=>Array("user_id"=>$session_UID, "friend_id"=>$friend_id, "relationship_type_id"=>$relTypeID)));
					___(" added to friends list");
				}
			}		
		}		
		exit;
	}
	
	
	function decline()
	{
		$type = $this->params['url']['type'];
		if (!in_array($type, $this->request_type))
			exit;
		
		$session_UID = $this->getLoggedInUserID();
		if (!$session_UID)
			exit;
			
		switch($type)
		{
			case 'join':
			
			$theme_request_id = $this->params['url']['rid'];
			$this->ThemeRequest->id = $theme_request_id;
			$tr = $this->ThemeRequest->read();
			$theme_id = $tr['ThemeRequest']['theme_id'];
			$to_id = $tr['ThemeRequest']['to_id'];
			
			if ($to_id !== $session_UID)
				exit;
			
			$this->ThemeRequest->saveField('status', 'declined');
			___("Message read and deleted");
			break;			
				
			case 'friend':
			
			$friend_request_id = $this->params['url']['rid'];
			$this->FriendRequest->id = $friend_request_id;
			$fr = $this->FriendRequest->read();
			$to_id = $fr['FriendRequest']['to_id'];
			$friend_id = $fr['FriendRequest']['user_id'];		
			
			if ($to_id !== $session_UID)
				exit;
			
			$this->FriendRequest->saveField('status', 'declined');
			$this->Notification->clear_memcached_notifications($session_UID, false, true);
			___("Message read and deleted");
		}		
		exit;
	}
}
?>
