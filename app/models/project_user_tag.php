<?php
Class ProjectUserTag extends AppModel {
    var $name = "ProjectUserTag";
	var $belongsTo = array(
							'Project' => array('className' => 'Project','fields'=>'id,name')
							,'Tag' => array('className' => 'Tag','fields'=>'id,name')
							);
	
}
?>
