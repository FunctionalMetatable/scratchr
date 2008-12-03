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
		$this->bindModel( array(
				'belongsTo' => array( 
					'Project' => array(
						'className' => 'Project',
						'foreignKey' => 'project_id',
						'fields' => array('IFNULL(Project.user_id, 0) project_owner_id',
										  'IFNULL(Project.name, "") project_name')
					),
					'Gallery' => array(
						'className' => 'Gallery',
						'foreignKey' => 'gallery_id',
						'fields' => array('IFNULL(Gallery.user_id, 0) gallery_owner_id',
										 'IFNULL(Gallery.name, "") gallery_name')
					)
				)
		));
		
		return $this->find('all', array(
									'conditions' => array('Notification.to_user_id' => $user_id,
													'Notification.status' => 'UNREAD'),
									'order' => 'Notification.id DESC'
									)
						);
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
}
?>