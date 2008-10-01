<?php
Class UserStat extends AppModel {
    var $name = "UserStat";
    var $belongsTo = array('User'=>array('className'=>'User'));

    function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('User' =>
            array('className' => 'Project'))));
    }
}
