<?php
Class Curator extends AppModel {
    var $name = "Curator";
    var $belongsTo = array('User'=>array('className'=>'User'));

    function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('User' =>
            array('className' => 'Favorite'))));
    }
}
