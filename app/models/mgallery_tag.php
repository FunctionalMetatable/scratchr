<?php
class MgalleryTag extends AppModel
{
    var $name = 'MgalleryTag';
    var $belongsTo = array('Tag' => array('className' => 'Tag'));
}
?>
