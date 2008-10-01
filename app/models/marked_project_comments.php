<?php
Class MarkedComment extends AppModel {
    var $name = 'MarkedProjectComment';
	var $belongsTo = array('User' => array('className' => 'User'), 'Pcomment' => array('className'=>'Pcomment'));
}
