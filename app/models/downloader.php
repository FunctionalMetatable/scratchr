<?php
Class Downloader extends AppModel {
    var $name = 'Downloader';
	
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
