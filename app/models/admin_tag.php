<?php
Class AdminTag extends AppModel {
    var $name = 'AdminTag';
    var $belongsTo = array('Tag' => array('className' => 'Tag'), 'User' => array('className' => 'User'));
	
	function set_status($id, $status) {
		if ($id) {
			$this->id = $id;
			$this->saveField('status', $status);
		}
	}
}
?>