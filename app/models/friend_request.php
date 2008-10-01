<?php
Class FriendRequest extends AppModel {
    var $name = 'FriendRequest';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>
