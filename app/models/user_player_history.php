<?php
Class UserPlayerHistory extends AppModel
{
	var $name = 'UserPlayerHistory';
		
		function getPlayerType($user_id) { 
			$option = $this->find('first', array('conditions' => array('user_id' => $user_id), 'fields'=> 'player_type', 'order' =>'created DESC'));
			if(!empty($option))
				return $option['UserPlayerHistory']['player_type'];
			else
				return null;
	
		}//eof
}//class

?>
