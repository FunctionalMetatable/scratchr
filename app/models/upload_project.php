<?php
Class UploadProject extends AppModel {
    var $name = "UploadProject";
	var $belongsTo = array('Project' => array('className' => 'Project','fields'=>'id,name'));
		
}
?>
