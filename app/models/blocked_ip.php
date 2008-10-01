<?php
class BlockedIp extends AppModel {
    var $name = 'BlockedIp';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>
