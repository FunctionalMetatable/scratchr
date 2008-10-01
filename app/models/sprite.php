<?php
Class Sprite extends AppModel {
    var $name = 'Sprite';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>
