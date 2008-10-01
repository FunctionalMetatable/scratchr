<?php
Class GalleryProject extends AppModel {

    var $name = "GalleryProject";
	var $belongsTo = array('Project' => array('className' => 'Project'), 'Gallery' => array('className' => 'Gallery'));
	
	function bindProject() {
        $this->bindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project'))));
	}
	
	function bindGallery() {
	  $this->bindModel(array(
        'belongsTo' => array(
            'Gallery' =>
                array('className' => 'Gallery'))));
	}
	
	function bindHABTMProject($conditions=null, $order=null, $limit=null, $page = 1) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array('Project' =>
			array('className'  => 'Project',
				'joinTable'  => 'gallery_projects',
				'foreignKey' => 'gallery_id',
				'associationForeignKey'=> 'project_id',
				'conditions' => $conditions,
				'limit' => $limit,
				'order' => $order,
				'page' => $page))));
    }
}
?>
