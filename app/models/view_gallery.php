<?php
Class ViewGallery extends AppModel {
    var $name = "ViewGallery";
	var $belongsTo = array('Gallery' => array('className' => 'Gallery','fields'=>'id,name'));
		
}
?>
