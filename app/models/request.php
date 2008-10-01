<?php
Class Request extends AppModel {
    var $name = 'Request';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>
