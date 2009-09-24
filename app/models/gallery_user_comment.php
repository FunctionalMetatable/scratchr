<?php
Class GalleryUserComment extends AppModel {
    var $name = "GalleryUserComment";
	var $belongsTo = array(
							'Gallery' => array('className' => 'Gallery','fields'=>'id,name')
							,'Gcomment' => array('className' => 'Gcomment','fields'=>'id,content')
							);
	
}
?>
