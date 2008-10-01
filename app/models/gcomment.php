<?php
class Gcomment extends AppModel {
    var $name = 'Gcomment';
     var $belongsTo = array('User' => array('className' => 'User'), 'Gallery' => array ('className' => 'Gallery'));
	
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

   function unbindUser($conditions = null, $order = null) {
        $this->unbindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
   }
}
?>
