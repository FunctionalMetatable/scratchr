<?php
class GalleryFlag extends AppModel {
	var $belongsTo = array('Gallery' => array ('className' => 'Gallery'));
    var $name = 'GalleryFlag';
}
?>
