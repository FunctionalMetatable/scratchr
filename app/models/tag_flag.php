<?php
class TagFlag extends AppModel
{
    var $name = 'TagFlag';
    var $belongsTo = array('Tag' => array('className' => 'Tag'));
}
?>
