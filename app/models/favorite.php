<?php
Class Favorite extends AppModel {
    var $name = 'Favorite';
	var $belongsTo = array('Project' => array('className' => 'Project'));
	
   function bindProject($conditions=null, $order=null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project',
					  'conditions' => $conditions,
					  'order' => $order,
					  'foreignKey' => 'project_id'))));
    }
	
	function bindUser($conditions=null, $order=null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User',
					  'conditions' => $conditions,
					  'order' => $order,
					  'foreignKey' => 'user_id'))));
    }
}
