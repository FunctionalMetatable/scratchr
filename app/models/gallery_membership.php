<?php
Class GalleryMembership extends AppModel {

    var $name = "GalleryMembership";
    var $belongsTo = array('User' => array('className' => 'User'), 'Gallery' => array('className' => 'Gallery'));
	
	function bindGallery($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('Gallery' =>
             array('className' => 'Gallery'))));
    }
    
	function unbindGallery($conditions = null, $order = null) {
        $this->unbindModel(array(
            'belongsTo' => array('Gallery' =>
             array('className' => 'Gallery',
                'conditions' => $conditions,
                'order' => $order
                ))));
    }
	
    function bindUser() {
		$this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User',
					'foreignKey' => 'user_id'))));
	}
}
?>
