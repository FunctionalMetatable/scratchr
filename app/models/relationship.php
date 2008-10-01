<?php
class Relationship extends AppModel
{
    var $name = 'Relationship';
	
	function bindType($conditions = null, $limit=null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'RelationshipType' =>
                array('className' => 'RelationshipType',
					'conditions' => $conditions,
					'limit' => $limit))));
	}
	
	function bindFriend() {
		$this->bindModel(array(
        'belongsTo' => array(
            'Friend' =>
                array('className' => 'User',
					'foreignKey' => 'friend_id'))));
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
