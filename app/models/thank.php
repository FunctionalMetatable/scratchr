<?php
class Thank extends AppModel {

	var $name = 'Thank';
	var $useTable = 'thanks';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'User' => array('className' => 'User',
								'foreignKey' => 'reciever_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
			
	);

}
?>