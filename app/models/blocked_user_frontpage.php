<?php
class BlockedUserFrontpage extends AppModel {
    var $name = 'BlockedUserFrontpage';
	var $useTable ='blocked_user_frontpages';
    var $belongsTo = array('User' => array('className' => 'User','foreignKey' => 'user_id'));
}
?>