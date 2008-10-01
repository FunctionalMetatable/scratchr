<?php
class BlockedUser extends AppModel {
    var $name = 'BlockedUser';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>