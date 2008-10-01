<?php
Class SpriteComment extends AppModel {
    var $name = 'SpriteComment';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>