<?php
Class Lover extends AppModel {
    var $name = 'Lover';
	var $belongsTo = array('User' => array('className' => 'User'));
	
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
?>
