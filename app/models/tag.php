<?php
Class Tag extends AppModel {
    var $name = 'Tag';
    /*var $hasMany = array("ProjectTag"=>
        array("className" => "ProjectTag",
            "foreignKey" => "tag_id"));
    */	
	
    function bindProjectTag($conditions=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'ProjectTag' =>
                array('className' => "ProjectTag",
                'foreignKey' => "tag_id",
                'conditions' => $conditions))));
    }
	
    /**
     * Join table HABTM association
     */
    function bindHABTMProject() {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Project' => array(
            'className' => 'Project',
            'joinTable' => 'project_tags',
            'foreignKey' => 'tag_id',
            'associationForeignKey' => 'project_id'))));
    }
}
?>
