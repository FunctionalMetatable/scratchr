<?php
Class GalleryRequest extends AppModel {
    var $name = 'GalleryRequest';
    var $belongsTo = array('User' => array('className' => 'User'), 'Gallery' => array('className'=>'Gallery'));
}
?>
