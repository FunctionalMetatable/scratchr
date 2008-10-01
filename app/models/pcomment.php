<?php
class Pcomment extends AppModel {
    var $name = 'Pcomment';
    var $belongsTo = array('User' => array('className' => 'User'), 'Project' => array ('className' => 'Project'));
    
    function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
                   /*'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'user_id'
                     ))));*/
    }
    
     function bindProject($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project'))));
    }
}
?>
