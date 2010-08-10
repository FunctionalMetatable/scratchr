<?php
class ExperimentalController extends AppController
{

	var $name = 'Experimental';
        var $uses = array('Project','User', 'ExperimentalUser', 'ExperimentalView', 'ExperimentalLog');


        // This controller handles all the relevant stuff for
        // the new flash based Experimental Viewer



        function _is_opted_in($userid = null) {
            if ($userid == null) {
                $this->Session->setFlash('<span class="notify">'.___('If you want to try the Experimental Viewer, please log in.', true).'</span>');
                $this->Session->write('experimentalviewerRedirect', 'TRUE');
                $this->Session->write('experimentalviewerRedirectTime', time());
                $this->redirect('/login');
            }

            return $this->ExperimentalUser->isOptedIn($userid);
        }




        function optinout() {
            // Catch all method which is associated with the opt in/out page
            $userid = $this->getLoggedInUserID();

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                $this->pageTitle = 'Opt in for the Scratch Experimental Viewer';
                $this->set('opted_in', TRUE);
            }
            else {
                $this->pageTitle = 'Opt out from the Scratch Experimental Viewer';
                $this->set('opted_in', FALSE);
            }
        }




        function optout() {
            $userid = $this->getLoggedInUserID();

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                $user = $this->ExperimentalUser->find('first', array('conditions' =>
                    array('ExperimentalUser.user_id' => $userid)));

                $user['ExperimentalUser']['enabled'] = 0;
                $this->ExperimentalUser->save($user);
                $this->Session->setFlash('<span class="notify">'.___('You have successfully opted out from the Experimental Viewer.', true).'</span>');
                $this->redirect('/');
            }
            else {
                $this->Session->setFlash('<span class="notify">'.___('You have already opted out for the Experimental Viewer or you never opted in', true).'</span>');
                $this->redirect('/');
            }
        }




        function optin() {
            $userid = $this->getLoggedInUserID();

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                $this->Session->setFlash('<span class="notify">'.___('You have already opted in for the Experimental Viewer.', true).'</span>');
                $this->redirect('/');
            }
            else {
                $user = $this->ExperimentalUser->find('first', array('conditions' =>
                    array('ExperimentalUser.user_id' => $userid)));
                if ($user) { // The user opted out at some point.
                    $user['ExperimentalUser']['enabled'] = 1;
                    $this->ExperimentalUser->save($user);
                }
                else {
                    $data = array();
                    $data['ExperimentalUser'] = array();
                    $data['ExperimentalUser']['id'] = null; /*A new record will be created*/
                    $data['ExperimentalUser']['user_id'] = $userid;
                    $data['ExperimentalUser']['enabled'] = TRUE;
                    $this->ExperimentalUser->save($data);
                }
                $this->Session->setFlash('<span class="notify">'.___('You have opted in to try out the Scratch Experimental Viewer', true).'</span>');
                $this->redirect('/');
            }
        }


        function viewproject($creatorname= null, $projectid = null) {
            $this->pageTitle = 'Scratch Experimental Viewer';
            $this->layout = 'empty';
            $userid = $this->getLoggedInUserID();

            if (!($userid) or !($projectid) or !($creatorname)) {
                $this->redirect('/'); // XXX: Should an error be set ?
            }

            $is_opted_in = $this->_is_opted_in($userid);

            if ($is_opted_in) {
                // We create a new entry everytime (even for repeat visits)
                $client_ip = $this->RequestHandler->getClientIP();
                $long = ip2long($client_ip);
                $data = array();
                $data['ExperimentalView'] = array();
                $data['ExperimentalView']['id'] = null;
                $data['ExperimentalView']['user_id'] = $userid;
                $data['ExperimentalView']['project_id'] = $projectid;
                $data['ExperimentalView']['ipaddress'] = $long;
                $this->ExperimentalView->save($data);

                $this->set('creatorname', $creatorname);
                $this->set('projectid', $projectid);
                $this->set('userid', $userid);
            }
            else {
                // Redundant, since is_opted_in() should have redirected earlier
                $this->redirect('/');
            }
        }

        function track($projectid = null, $userid = null, $actionname = null) {
            if (!$projectid or !$userid or !$actionname)
                die;

            $client_ip = $this->RequestHandler->getClientIP();
            $long = ip2long($client_ip);
            $data = array();
            $data['ExperimentalLog'] = array();
            $data['ExperimentalLog']['id'] = null;
            $data['ExperimentalLog']['user_id'] = $userid;
            $data['ExperimentalLog']['project_id'] = $projectid;
            $data['ExperimentalLog']['action'] = $actionname;
            $data['ExperimentalLog']['ipaddress'] = $long;
            $this->ExperimentalLog->save($data);
            die;
        }

}

?>
