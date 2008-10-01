<?php
Class Theme extends AppModel {

    var $name = 'Theme';
    var $belongsTo = array('User' => array('className' => 'User'));

	function remove($theme_id) {
		// delete theme icon
		// delete theme_memberships
		// delete theme table
		// delete theme_projects
		// delete tcomments
		$this->bindTcomment();
		$this->bindHABTMProject();
		$this->bindHABTMMembers();		
		$this->bindFeatured();
		$this->delete($theme_id, true);
        $icon_file = WWW_ROOT . getThemeIcon($theme_id, false, DS);
		if (file_exists($icon_file))
			unlink($icon_file);	
		return true;
	}
		
	function bindTcomment($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Tcomment' =>
                array('className' => 'Tcomment',
					'dependent' => true, 
                    'conditions' => $conditions))));
    }
	
    function bindHABTMProject($conditions=null, $order=null, $limit=null, $page = 1) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array('Project' =>
			array('className'  => 'Project',
				'joinTable'  => 'theme_projects',
				'foreignKey' => 'theme_id',
				'associationForeignKey'=> 'project_id',
				'conditions' => $conditions,
				'limit' => $limit,
				'order' => $order,
				'page' => $page))));
    }

    function bindHABTMMembers($conditions=null, $order=null, $limit=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array('User' =>
			array('className'  => 'User',
				'joinTable'  => 'theme_memberships',
				'foreignKey' => 'theme_id',
				'associationForeignKey'=> 'user_id',
				'conditions' => $conditions,
				'limit' => $limit,
				'order' => $order))));
    }
	
	
	function bindFeatured() {
        $this->bindModel(array(
        'hasOne' => array(
            'FeaturedTheme' =>
                array('className' => 'FeaturedTheme',
					'dependent' => true))));	
	}
	
	 function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
	 }
}
?>
