<?php
class PermissionUser extends AppModel {

	var $name = 'PermissionUser';
	var $useTable = 'permission_users';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'User' => array('className' => 'User',
								'foreignKey' => 'user_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			),
			'Permission' => array('className' => 'Permission',
								'foreignKey' => 'permission_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);

}
?>