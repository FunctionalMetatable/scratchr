<?php
Class Flagger extends AppModel {
    var $name = 'Flagger';
	
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
