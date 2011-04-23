<?php
/**
 * Methods for the template based user notification system
 *
 * @author	Anupom Syam
 */
class Notification extends AppModel {
  
	var $belongsTo = array('NotificationType' =>
						   array('className'    => 'NotificationType',
								 'foreignKey'   => 'notification_type_id'
						   )
					);
	
	/**
	* Returns all notifications to a user specified by $user_id
	*
	* @param int $user_id	specifies the id of the user
	* @return mixed	all notifications set to a user
	*/
	function getNotifications($user_id, $page, $limit, $admin = false) {
		$notifications = false;
		$nstatus_conditions = '';
		$rstatus_conditions = '';
		
		if(!$admin) {
			$nstatus_conditions = ' AND `Notification`.`status` = "UNREAD"';
			$rstatus_conditions = ' AND Request.status = "pending"';
		}
		
		$nstatus_conditions .= ' AND NotificationType.negative=0';
		
		//try collecting first page from memcache
		if($page==1 && !$admin) {
			$this->mc_connect();
			$notifications = $this->mc_get('notifications_page1', $user_id);
		}
		if ($notifications === false) {
			$offset = ($page -1) * $limit;
            $notification_query = $this->__createNotificationQuery($user_id, $nstatus_conditions);
				
			$request_query = 
				'SELECT `Request`.`id`, `User`.`username` `from_user_name`, `Request`.`created_at` `created`,'
				.' `Request`.`to_id` `to_user_id`, NULL, NULL, NULL, NULL, NULL, NULL,'
				.' `Request`.`status`,  NULL,  NULL,  NULL,  NULL, 0, NULL,  0, NULL,'
				.' "request" notif_type'
				.' FROM friend_requests as Request LEFT JOIN users as User ON User.id = Request.user_id'
				.' WHERE Request.to_id = '.$user_id.$rstatus_conditions
				.' ORDER BY `created` DESC';
			
			$query = '( ' . $notification_query . ' )'
					.' UNION ALL (' . $request_query . ')'
					.' ORDER BY `created` DESC LIMIT '.$offset.', '.$limit;
			
			$notifications = $this->query($query);
			//storing first page in memcache
			if($page==1 && !$admin) {
				$this->mc_set('notifications_page1', $notifications, $user_id, 10);
			}
		}
        
		if($page==1 && !$admin) { $this->mc_close(); }
		return $notifications;
	}
	
	function getInappropriateNotifications($user_id, $page, $limit) {
		if($page == 1 && !$admin)
		{
			$this->mc_connect();
			$notifications = $this->mc_get('adminnotifications_page1', $user_id);
		}
		if($notifications === false)
		{
		$inappropriate_conditions = ' AND `NotificationType`.`negative` = 1';
		$offset = ($page -1) * $limit;
		$notification_query = $this->__createNotificationQuery($user_id, $inappropriate_conditions);
        $notification_query .= ' LIMIT ' . $offset . ', ' . $limit;
		$notifications = $this->query($notification_query);
		if($page == 1 && !$admin)
		{
			$this->mc_set('adminnotifications_page1',$notifications,$user_id,10);
		}
		}
		if($page == 1 && !$admin)
			$this->mc_close();
		return $notifications;
	}
	
	// Marks all notifications of a user, $user_id, read, except negatives
	function readAllExceptAdmin($user_id)
	{
	     $query = "UPDATE `notifications` 
	     LEFT JOIN `notification_types` on `notifications`.`notification_type_id` = `notification_types`.`id` 
	     SET `notifications`.`status` = 'READ' 
	     WHERE `notifications`.`to_user_id`=$user_id 
	     AND `notification_types`.`negative` = 0";
	     $this->query($query);
	     
	}
	
	function countAllNotification($user_id) {
		
		$nstatus_conditions = ' AND `NotificationType`.`negative` = 1';;
		$ncount = $this->findCount("Notification.to_user_id =".$user_id . $nstatus_conditions);
		
		
		return intval($ncount);
	}
	
	/**
	* returns number of unread/pending notifications/friend_requests
	* Precondition: must be logged in
	*
	* @return int	number of total 
	*/
	function countAll($user_id, $admin = false) {
		$notification_count = false;
		$nstatus_conditions = '';
		$rstatus_conditions = '';
		
		//for admin we dont want to use memcache, for no admins we want to select only unread ones
		if(!$admin) {
			$this->mc_connect();
			$notification_count = $this->mc_get('notification_count', $user_id);
			$nstatus_conditions = ' AND `status` = "UNREAD"';
			$rstatus_conditions = ' AND `status` = "pending" ';
		}
		
		if ($notification_count === false) {
			$notification_query =  'SELECT COUNT( id ) AS count FROM `notifications`'
								.' WHERE `to_user_id` = '. $user_id . $nstatus_conditions;
			$ncount = $this->query($notification_query);
			$request_query = 'SELECT COUNT( id ) AS count FROM `friend_requests`'
							.' WHERE `to_id` = '. $user_id . $rstatus_conditions;
			$rcount = $this->query($request_query);
			$notification_count = ($ncount[0][0]['count'] + $rcount[0][0]['count']);
			
			if(!$admin) {
				$this->mc_set('notification_count', $notification_count, $user_id, 10);
			}
		}
		
		if(!$admin) { $this->mc_close(); }
		
		return intval($notification_count);
	}
	
	/**
	* saves a notification
	*
	* @param string $tyoe		specifies the type of the notification
	* @param int $to_user_id	id of the user to whom we want to set the notification
	* @param mixed $data		contains various other related notification template parameters. It may contain,
	* $data[from_user_name]	string contains the name of the user from whom this notification is sent
	* $data[project_id ]		int contains the related project id
	* $data[project_owner_name]	string contains the related project's owner's name
	* $data[gallery_id ]		int contains related gallery id
	* @param array $extra		array	containing fields to be saved in the extra field
	* @return int			id of the notification type
	*/
	function addNotification($type, $to_user_id, $data, $extra = array()) {
		$notification_type_id = $this->__getNotificationTypeId($type);
		if(!$notification_type_id) return false;
		
		$data['to_user_id'] = $to_user_id;
		$data['notification_type_id'] = $notification_type_id;
		if(!empty($extra)) {
			$data['extra'] = implode('|', $extra);
		}
		
		$this->create();
		$this->save($data);
	}

    /**
	* saves bulk notifications sent from admin panel to a set of users
	* @param string $usernames  comma separated usernames
    * @param string $text       text of the notification
	*/
    function addBulkNotifications($to_usernames, $text) {
        //get notification type id for bulk
        $notification_type_id = $this->__getNotificationTypeId('bulk');

        //get to_user_ids from to_usernames
        $to_usernames = trim($to_usernames, ', ');
        
        $patterns = array('/(\s*)(\w+)(\s*)/i', '/(\s*)(\w+)(\s*),(\s*)/i');
        $replacements = array('"${2}"', '"${2}",');
        $to_usernames = preg_replace($patterns, $replacements, $to_usernames);
      
        $users = $this->query('SELECT id, username FROM `users` WHERE `username` IN ('.$to_usernames.')');

        $all_users = explode('","', trim($to_usernames,'"'));
        $valid_users = array();
        $data = array();
        foreach($users as $user) {
            $data[] = array('to_user_id' => $user['users']['id'],
                            'extra' => $text,
                            'notification_type_id' => $notification_type_id,
            );
            $valid_users[] = $user['users']['username'];
        }
        $invalid_users = array_diff($all_users, $valid_users);
        
        $ok = false;
        if(!empty($data)) {
            $this->create();
            $ok = $this->saveAll($data);
        }

        return array('valids' => $valid_users, 'invalids' => $invalid_users);
    }

    function addBulkNotificationsByCondition($condition, $text) {
        //get notification type id for bulk
        $notification_type_id = $this->__getNotificationTypeId('bulk');
        $users = $this->query('SELECT id, username FROM `users` WHERE '.$condition);
        $valid_users = array();
        $data = array();
        foreach($users as $user) {
            $data[] = array('to_user_id' => $user['users']['id'],
                            'extra' => $text,
                            'notification_type_id' => $notification_type_id,
            );
             $valid_users[] = $user['users']['username'];
        }
        $ok = false;
        if(!empty($data)) {
            $this->create();
            $ok = $this->saveAll($data);
        }
        return array('valids' => $valid_users, 'invalids' => array());
    }
	
	/**
	* Returns notification type id from notification type string to a user specified by $user_id
	*
	* @param string $tyoe	specifies the type of the notification
	* @return int		id of the notification type
	*/
	function __getNotificationTypeId($type) {
		//TODO: use memcache to load all types
		$notification_type = $this->NotificationType->find('first', array('fields' => 'id',
									'conditions' => array('type' => $type)));
		return $notification_type['NotificationType']['id'];
		
	}

    function __createNotificationQuery($user_id, $extra_conditions) {
      $sql = 'SELECT `Notification`.`id`, `Notification`.`from_user_name`, `Notification`.`created`,'
                .' `Notification`.`to_user_id`,  `Notification`.`project_id`, `Notification`.`project_owner_name`,'
                .' `Notification`.`gallery_id`, `Notification`.`comment_id`,'
                .' `Notification`.`extra`, `Notification`.`notification_type_id`,'
                .' `Notification`.`status`, `NotificationType`.`id` type_id, `NotificationType`.`type`,'
                .' `NotificationType`.`template`, `NotificationType`.`is_admin`,'
                .' IFNULL(Project.user_id, 0) project_owner_id, IFNULL(Project.name, "") project_name,'
                .' IFNULL(Gallery.user_id, 0) gallery_owner_id, IFNULL(Gallery.name, "") gallery_name,'
                .' "notification" notif_type'
                .' FROM `notifications` AS `Notification`'
                .' LEFT JOIN `notification_types` AS `NotificationType` ON'
                .' (`Notification`.`notification_type_id` = `NotificationType`.`id`)'
                .' LEFT JOIN `projects` AS `Project` ON (`Notification`.`project_id` = `Project`.`id`)'
                .' LEFT JOIN `galleries` AS `Gallery` ON (`Notification`.`gallery_id` = `Gallery`.`id`)'
                .' WHERE `Notification`.`to_user_id` = '.$user_id.$extra_conditions
                .' ORDER BY `created` DESC';

      return $sql;
    }
	
	/**
	* clears notification/requests items stored in memcache
	*
	* @param int $user_id			specifies the user's id - who's notifications/requests need to be cleared
	* @param bool $clear_notifs		true when we want to clear notification related items, false otherwise
	* @param bool $clear_requests	true when we want to clear request related items, false otherwise
	*/
	function clear_memcached_notifications($user_id) {
		$this->mc_connect();
        $this->mc_delete('notifications_page1', $user_id);
	$this->mc_delete('adminnotifications_page1', $user_id);
        $this->mc_delete('notification_count', $user_id);
        $this->mc_close();
	}
}
?>
