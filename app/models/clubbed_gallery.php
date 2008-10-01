<?php
Class ClubbedGallery extends AppModel {
    var $name = "ClubbedGallery";
    var $belongsTo = array('Gallery' => array('className' => 'Gallery'));

    function bindGallery($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('Gallery' =>
             array('className' => 'Gallery',
                'conditions' => $conditions,
                'order' => $order
                ))));
    }
}
?>
