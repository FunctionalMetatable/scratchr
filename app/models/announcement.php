<?php
class Announcement extends AppModel {
    var $name = 'Announcement';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>