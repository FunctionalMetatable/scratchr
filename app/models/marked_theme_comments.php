<?php
Class MarkedThemeComment extends AppModel {
    var $name = 'MarkedThemeComment';
	var $belongsTo = array('User' => array('className' => 'User'), 'Tcomment' => array('className'=>'Tcomment'));
}
