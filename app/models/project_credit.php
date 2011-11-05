<?php
Class ProjectCredit extends AppModel {
    var $name = 'ProjectCredit';
    var $belongsTo = array('Project' => array('className' => 'Project'));
}
?>
