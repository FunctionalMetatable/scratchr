<?php
Class SpriteTag extends AppModel {
    var $name = 'SpriteTag';
    var $belongsTo = array('Tag' => array('className' => 'Tag'));
}
?>
