<?php
Class ViewStat extends AppModel {
    var $name = 'ViewStat';
    var $belongsTo = array('Project' => array('className' => 'Project'));

    /*
    function bindProject() {
        $this->unbindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project'))));
    }*/
}
?>
