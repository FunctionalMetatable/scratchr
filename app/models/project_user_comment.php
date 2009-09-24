<?php
Class ProjectUserComment extends AppModel {
    var $name = "ProjectUserComment";
	var $belongsTo = array(
							'Project' => array('className' => 'Project','fields'=>'id,name')
							,'Pcomment' => array('className' => 'Pcomment','fields'=>'id,content')
							);
	
}
?>
