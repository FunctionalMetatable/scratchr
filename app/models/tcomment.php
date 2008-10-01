<?php
class Tcomment extends AppModel {
    var $name = 'Tcomment';
    var $belongsTo = array('User' => array('className' => 'User'));
    
    function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
    }
}
?>