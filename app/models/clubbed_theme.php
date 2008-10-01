<?php
Class ClubbedTheme extends AppModel {
    var $name = "ClubbedTheme";
    var $belongsTo = array('Theme' => array('className' => 'Theme'));

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
