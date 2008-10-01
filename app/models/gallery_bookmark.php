<?php
Class GalleryBookmark extends AppModel {

    var $name = "GalleryBookmark";
    var $belongsTo = array('User' => array('className' => 'User'));
	
	function bindGallery($conditions = null, $order = null) {
        $this->bindModel(array(
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
