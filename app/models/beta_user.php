<?php

Class BetaUser extends AppModel
{
	var $name = 'BetaUser';

        function isOptedIn($userid = null) {
            if ($userid == null) {
                return FALSE;
            }

            $user = $this->find('first', array('conditions' =>
                array('BetaUser.user_id' => $userid)));

            if ($user) {
                // Already opted in
                if ($user['BetaUser']['enabled'] == 1)
                    return TRUE;
                else // Opted out earlier
                    return FALSE;
            }
            else // No records found in beta_user table
                return FALSE;
        }
}

?>
