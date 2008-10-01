<?php
class Duser extends AppModel
{
	var $name = 'Duser';
	var $validate = array(
        	'firstname'	=> VALID_NOT_EMPTY,
        	'lastname'	=> VALID_NOT_EMPTY,
        	'email'		=> VALID_EMAIL,
			'organization'  => VALID_NOT_EMPTY
    	);

}
?>