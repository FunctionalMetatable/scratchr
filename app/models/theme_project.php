<?php
Class ThemeProject extends AppModel {

    var $name = "ThemeProject";
    //var $belongsTo = array('Project' => array('className' => 'Project'));
	
	function bindProject() {
        $this->bindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project'))));
	}
}
?>
