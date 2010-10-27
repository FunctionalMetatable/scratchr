<?php
Class UserWelcome extends AppModel
{
	var $name = 'UserWelcome';
	var $useTable = 'users_welcoming';

        function getOption() {
            $result = $this->find('first', array('order'=>"id DESC",'fields'=>'option'));
			$option = $result['UserWelcome']['option'];
			if(empty($option) || $option == WELCOME_EXPERIMENT_TOTAL_OPTIONS)
				$setOption = 1;
			else
				$setOption = intval($option)+1;
			return $setOption;

        }//function
}//class

?>
