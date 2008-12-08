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
	
	function getProjectTagCloud( $limit ) {
		$query = 'SELECT Tag.name, COUNT( tt.project_id ) AS tagcounter'
				.' FROM project_tags tt, tags Tag '
				.' WHERE Tag.id = tt.tag_id '
				.' GROUP BY Tag.id '
				.' ORDER BY tagcounter DESC '
				.' LIMIT ' . $limit;
		
		return $this->query($query);
	}
}
?>
