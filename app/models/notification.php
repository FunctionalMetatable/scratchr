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
	function getNotifications($user_id) {
		$notification_query = 
			'SELECT `Notification`.`id`, `Notification`.`from_user_name`, `Notification`.`created`,'
			.' `Notification`.`to_user_id`,  `Notification`.`project_id`, `Notification`.`project_owner_name`,'
			.' `Notification`.`gallery_id`, `Notification`.`extra`, `Notification`.`notification_type_id`,'
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
			.' WHERE `Notification`.`to_user_id` = '.$user_id.' AND `Notification`.`status` = "UNREAD"';
		
		$request_query = 
			'SELECT `Request`.`id`, `User`.`username` `from_user_name`, `Request`.`created_at` `created`,'
			.' `Request`.`to_id` `to_user_id`, NULL, NULL, NULL, NULL, NULL,'
			.' `Request`.`status`,  NULL,  NULL,  NULL,  NULL, 0, NULL,  0, NULL,'
			.' "request" notif_type'
			.' FROM friend_requests as Request LEFT JOIN users as User ON User.id = Request.user_id'
			.' WHERE Request.to_id = '.$user_id.' AND Request.status = "pending"';
		
		$query = 'SELECT * FROM ( ' . $notification_query . ' ) As Notif'
				.' UNION ' . $request_query . ' ORDER BY `created` DESC';
		
		return $this->query($query);
	}
	
	/**
	* returns number of unread/pending notifications/friend_requests
	*
	* @return int	number of total 
	*/
	function countAll( $user_id ) {
		$notification_query =  'SELECT COUNT( id ) AS count FROM `notifications`'
							.' WHERE `to_user_id` = '. $user_id .' AND `status` = "UNREAD"';
		$ncount = $this->query($notification_query);
		$request_query = 'SELECT COUNT( id ) AS count FROM `friend_requests`'
						.' WHERE `to_id` = '. $user_id .' AND `status` = "pending" ';
		$rcount = $this->query($request_query);
		return ($ncount[0][0]['count'] + $rcount[0][0]['count']);
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
	
	/**
	* clears notification/requests items stored in memcache
	*
	* @param int $user_id			specifies the user's id - who's notifications/requests need to be cleared
	* @param bool $clear_notifs		true when we want to clear notification related items, false otherwise
	* @param bool $clear_requests	true when we want to clear request related items, false otherwise
	*/
	function clear_memcached_notifications($user_id) {
		$memcache = new Memcache;
		$memcache->connect('localhost', 11211) or die ("Could not connect");
		$memcache->delete(MEMCACHE_PREFIX.'-notifications-'.$user_id);
		$memcache->delete(MEMCACHE_PREFIX.'-notification_count'.-$user_id);
		$memcache->close();
	}
}
?>