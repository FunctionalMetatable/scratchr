<?php
class RelationshipType extends AppModel
{
    var $name = 'RelationshipType';
	
	function bindRelationships($condition=null, $limit=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Relationship' =>
                array('className' => 'Relationship',
					'conditions' => $condition,
					'limit' => $limit,
					'order' => $order))));
	}
}
?>
