<?php
Class ThemeRequest extends AppModel {
    var $name = 'ThemeRequest';
    var $belongsTo = array('User' => array('className' => 'User'), 'Theme' => array('className'=>'Theme'));
}
?>
