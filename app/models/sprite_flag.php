<?php
Class SpriteFlag extends AppModel {
    var $name = 'SpriteFlag';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>