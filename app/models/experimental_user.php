<?php

Class ExperimentalUser extends AppModel
{
	var $name = 'ExperimentalUser';

        function isOptedIn($userid = null) {
            if ($userid == null) {
                return FALSE;
            }

            $user = $this->find('first', array('conditions' =>
                array('ExperimentalUser.user_id' => $userid)));

            if ($user) {
                // Already opted in
                if ($user['ExperimentalUser']['enabled'] == 1)
                    return TRUE;
                else // Opted out earlier
                    return FALSE;
            }
            else // No records found in experimenal_user table
                return FALSE;
        }
}

?>
