<?php
Class ViewProject extends AppModel {
    var $name = "ViewProject";
	var $belongsTo = array('Project' => array('className' => 'Project','fields'=>'id,name'));
		
}
?>
