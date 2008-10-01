<?php
class KarmaRating extends AppModel {
    var $name = 'KarmaRating';
    var $belongsTo = array('User' => array('className' => 'User'));
}
?>