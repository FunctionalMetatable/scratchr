<?php
Class FeaturedProject extends AppModel {
    var $name = "FeaturedProject";
    var $belongsTo = array('Project' => array('className' => 'Project'));

    function bindProject($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('Project' =>
             array('className' => 'Project',
                'conditions' => $conditions,
                'order' => $order
                ))));
    }
}
?>
