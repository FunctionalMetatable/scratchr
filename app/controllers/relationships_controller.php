<?php
Class RelationshipsController extends AppController {
    var $uses = array("Project","RelationshipType", "Relationship", "Gallery", "GalleryMembership", "User", "FriendRequest", "Notification"); 
    var $helpers = array('Pagination','Ajax','Javascript');
    var $components = array('Pagination');
	
	/**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
	/**
	 * Adds a one way relationship between users and
	 * sends a request for a two way relationship
	 */
	function request()
	{
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || empty($this->params['url']['type']) || empty($this->params['form']['friend-id']))
			exit;
				
		$friend_id = $this->params['form']['friend-id'];
		$friend = $this->User->find("id = $friend_id");
		if (empty($friend))
			exit;
		
		$type = $this->params['url']['type'];
		
		switch($type) {
		
		case 'friend':
			$relType = $this->RelationshipType->find("name = 'friend'"); 
			if (!empty($relType)) {
				
				$relTypeID = $relType['RelationshipType']['id'];
				if (!$this->Relationship->hasAny("user_id = ".$user_id." AND friend_id = ".$friend_id." AND relationship_type_id = ".$relTypeID)) {	
					$this->Relationship->create();
					$this->Relationship->save(Array("Relationship"=>Array("user_id"=>$user_id, "friend_id"=>$friend_id, "relationship_type_id"=>$relTypeID,'timestamp'=>NULL)));
					$this->FriendRequest->create();
					$this->FriendRequest->save(array("FriendRequest"=>array("user_id"=>$user_id, "to_id"=>$friend_id, "status"=>"pending",'created_at'=>NULL)));
					//echo "friend request sent";
					
					//Allow  recent friend to add project to my gallary
					$galleries = $this->Gallery->findAll("Gallery.user_id = $user_id AND Gallery.type = 3 AND Gallery.usage = 'friends' AND Gallery.visibility = 'visible'");
					//echo "<pre>";print_r($galleries);echo "</pre>"; exit;
					foreach($galleries as $gallery)
					{
						if (!$this->GalleryMembership->hasAny("GalleryMembership.user_id = ".$friend_id." AND GalleryMembership.gallery_id = ".$gallery['Gallery']['id'])) {
							$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $friend_id, 'gallery_id' => $gallery['Gallery']['id'], 'type' => 3, 'rank' => 'member'));
							$this->GalleryMembership->save($info);
							$this->GalleryMembership->id = false;
						}
					}
				}
			}
		}
        exit;
	}
	
	
	/**
	 * Completes a two way relationship between users
	 */
	function add() {	
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || empty($this->params['url']['type']) || empty($this->params['form']['friend-id']))
			exit;
				
		$friend_id = $this->params['form']['friend-id'];
		$friend = $this->User->find("id = $friend_id");
		if (empty($friend))
			exit;
		
		$type = $this->params['url']['type'];
		
		switch($type) {
		
		case 'friend':
			$relType = $this->RelationshipType->find("name = 'friend'"); 
			if (!empty($relType)) {
				
				$relTypeID = $relType['RelationshipType']['id'];
				if (!$this->Relationship->hasAny("user_id = ".$user_id." AND friend_id = ".$friend_id." AND relationship_type_id = ".$relTypeID)) {	
					$this->Relationship->create();
					$this->Relationship->save(Array("Relationship"=>Array("user_id"=>$user_id, "friend_id"=>$friend_id, "relationship_type_id"=>$relTypeID,'timestamp'=>NULL)));
					$this->Relationship->create();
					$this->Relationship->save(Array("Relationship"=>Array("user_id"=>$friend_id, "friend_id"=>$user_id, "relationship_type_id"=>$relTypeID,'created_at'=>NULL)));
					
					//functionality for updating GalleryMemberships
					$from_galleries = $this->Gallery->findAll("user_id = $user_id");
					$to_galleries = $this->Gallery->findAll("user_id = $friend_id");
					foreach ($from_galleries as $gallery) {
						if ($gallery['Gallery']['type'] == 3) {
							$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $friend_id, 'gallery_id' => $gallery_id, 'type' => 2));
							$this->Gallery->save($info);
						}
					}
					
					foreach ($to_galleries as $gallery) {
						if ($gallery['Gallery']['type'] == 3) {
							$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'type' => 2));
							$this->Gallery->save($info);
						}
					}
		
					echo "friend added";
				}
			}
		}
        exit;
	}
	
	
	/**
	 * Removes a one way relationship between users
	 */
	function remove($relationship_id) {
		$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
			exit;

		$type = $this->params['url']['type'];
		
		switch($type) {
		
		case 'friend':
			$relType = $this->RelationshipType->find("name = 'friend'"); 
			if (!empty($relType)) 
			{			
				$relationship = $this->Relationship->find("id = $relationship_id");
				$user_id = $relationship['Relationship']['user_id'];
				$friend_id = $relationship['Relationship']['friend_id'];
				$delete = false;
				
				if (!empty($relationship))
				{
					if ($this->isAdmin() || $session_user_id === $user_id)
					{
						$delete = true;
					}
				}
				
				if ($delete)
				{
					$this->Relationship->del($relationship_id);
				}
			}
		}
        exit;
	}
	
	
	/**
	 * Renders a listing of all the friends of the given user
	 * @param int $user_id => user id
	 */
	function friends($user_id) {
		$user_record = $this->User->find("id = ".$this->Sanitize->paranoid($user_id));
		
		// pagination
		$this->modelClass = "Relationship";
		$options = Array("url"=>"/relationships/friends/".$user_id);
		list($order,$limit,$page) = $this->Pagination->init(null, $options);
		
		$relType = $this->RelationshipType->find("name = 'friend'"); 
		$friends = null;
		if (!empty($relType)) {
			$relTypeID = $relType['RelationshipType']['id'];
			$this->Relationship->bindFriend();
			$friends = $this->Relationship->findAll("user_id = ".$user_id." AND relationship_type_id = ".$relTypeID, NULL, $order, $limit, $page, 2);
		} else
			$friends = Array();
			
		$this->set('friends',$friends);
		$this->set('whosFriend', $this->activeSession($user_id) ? "My Friends":$user_record['User']['urlname']."'s Friends");
		$this->render('friends');
	}
}
?>
