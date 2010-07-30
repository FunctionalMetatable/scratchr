<?php
class ExperimentalController extends AppController
{

	var $name = 'Experimental';
        var $uses = array('Project','User', 'BetaUser', 'BetaView');


        // This controller handles all the relevant stuff for
        // the new flash based experimental viewer



        function _is_opted_in($userid = null) {
            if ($userid == null) {
                $this->Session->setFlash(___('You need to be logged in to opt out of the experimental viewer.', true));
                $this->redirect('/login');
                return FALSE; // XXX: Is this needed ?
            }

            $user = $this->BetaUser->find('first', array('conditions' =>
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




        function optinout() {
            // Catch all method which is associated with the opt in/out page
            $userid = $this->getLoggedInUserID();

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                $this->pageTitle = 'Opt in for the beta Scratch viewer';
                $this->set('opted_in', TRUE);
            }
            else {
                $this->pageTitle = 'Opt out from the beta Scratch viewer';
                $this->set('opted_in', FALSE);
            }
        }




        function optout() {
            $userid = $this->getLoggedInUserID();

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                $user = $this->BetaUser->find('first', array('conditions' =>
                    array('BetaUser.user_id' => $userid)));

                $user['BetaUser']['enabled'] = 0;
                $this->BetaUser->save($user);
                $this->Session->setFlash(___('You have successfully opted out from the beta viewer.', true));
                $this->redirect('/');
            }
            else {
                $this->Session->setFlash(___('You have already opted out for the beta viewer or you never opted in', true));
                $this->redirect('/');
            }
        }




        function optin() {
            $userid = $this->getLoggedInUserID();

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                $this->Session->setFlash(___('You have already opted in for the beta viewer.', true));
                $this->redirect('/');
            }
            else {
                $user = $this->BetaUser->find('first', array('conditions' =>
                    array('BetaUser.user_id' => $userid)));
                if ($user) { // The user opted out at some point.
                    $user['BetaUser']['enabled'] = 1;
                    $this->BetaUser->save($user);
                }
                else {
                    $data = array();
                    $data['BetaUser'] = array();
                    $data['BetaUser']['id'] = null; /*A new record will be created*/
                    $data['BetaUser']['user_id'] = $userid;
                    $data['BetaUser']['enabled'] = TRUE;
                    $this->BetaUser->save($data);
                }
                $this->Session->setFlash(___('Congratulations! You have now opted in for the beta viewer.', true));
                $this->redirect('/');
            }
        }


        function viewproject($creatorname= null, $projectid = null) {
            $this->pageTitle = 'Beta Scratch viewer';

            $userid = $this->getLoggedInUserID();

            if (!($userid) or !($projectid) or !($creatorname)) {
                $this->redirect('/'); // XXX: Should an error be set ?
            }

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                // We create a new entry everytime (even for repeat visits)
                $data = array();
                $data['BetaView'] = array();
                $data['BetaView']['id'] = null;
                $data['BetaView']['user_id'] = $userid;
                $data['BetaView']['project_id'] = $projectid;
                $this->BetaView->save($data);

                $this->set('creatorname', $creatorname);
                $this->set('projectid', $projectid);
            }
            else {
                // Redundant, since is_opted_in() should have redirected earlier
                $this->redirect('/');
            }
        }

}

?>