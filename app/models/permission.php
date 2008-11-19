<?php
Class Permission extends AppModel {

    var $name = 'Permission';
	//var $useTable = 'permissions';
    var $hasAndBelongsToMany = array('User' =>
                               array('className'    => 'User',
                                     'joinTable'    => 'permission_users',
                                     'foreignKey'   => 'permission_id',
                                     'associationForeignKey'=> 'user_id',
                                     'conditions'   => '',
                                     'order'        => '',
                                     'limit'        => '',
                                     'unique'       => true,
                                     'finderQuery'  => '',
                                     'deleteQuery'  => '',
                               )
                               );
	

	
}
?>
