<?php
class ProjectFlag extends AppModel {
	var $belongsTo = array('Project' => array ('className' => 'Project'));
    var $name = 'ProjectFlag';
	
	function set_admin($id, $admin_id) {
		if ($id) {
			$this->id = $id;
			$this->saveField('admin_id', $admin_id);
		}
	}
	
	function set_feature_admin($id, $admin_id) {
		if ($id) {
			$this->id = $id;
			$this->saveField('feature_admin_id', $admin_id);
		}
	}
	
	function set_admin_time($id, $admin_time) {
		if ($id) {
			$this->id = $id;
			$this->saveField('feature_timestamp', $admin_time);
		}
	}
}
?>
