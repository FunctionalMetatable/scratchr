<?php
class IgnoredUser extends AppModel {
    var $name = 'IgnoredUser';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>
