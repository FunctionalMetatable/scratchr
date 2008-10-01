<?php
Class ThemeMembership extends AppModel {

    var $name = "ThemeMembership";
    var $belongsTo = array('User' => array('className' => 'User'));
	
	function bindTheme($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('Theme' =>
             array('className' => 'Theme',
                'conditions' => $conditions,
                'order' => $order
                ))));
    }
}
?>
