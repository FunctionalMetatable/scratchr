<?php
   
class ElectionsController extends AppController {
   
	var $name = 'elections';
	var $uses = array("Project","User","Pcomment","Gcomment", 'Election');
	
	// Enables/disables election based on the time
	// Modify this for new elections.
	function enabled() {
	    date_default_timezone_set('EDT');
        $startTime = mktime(11, 0, 0, 4, 11, 2012); // example: 11AM EDT 4/11/2012
        $endTime   = mktime(23, 0, 0, 4, 11, 2012); // example: 11PM EDT 4/11/2012
        return (time() > $startTime && time() < $endTime);
	}
	
	// Utility - show the server time
	function time() {
	    date_default_timezone_set('EDT');
	    echo date("r", time());
	    die();
	}
	
	// Redirects the user to the right page--if qualified to vote, the vote page.
	// Otherwise go to an explanation page.
	function vote() {
	    if (!$this->enabled()) {
	        $this->cakeError('error404');
	    }
		$username = $this->getLoggedInUsername();
		if (!$username) {
			$this->redirect('/login');
			return;
		}
		if ($this->validatevote($this->getLoggedInUserID())) {
			$this->set('user', $username);
			$this->render('vote', 'scratchr_userpage');
		} else {
			$this->redirect(INFO_URL . '/Community_Moderator_Election_New_Scratcher');
		}
	}

    // Saves a vote if we are qualified after a form submission
	function sendvote() {
	    if (!$this->enabled()) {
	        $this->cakeError('error404');
	    }
		$username = $this->getLoggedInUsername();
		if (!$username || !$this->validatevote($this->getLoggedInUserID())) {
			$this->redirect('/login');
			return;
		}
		
		$previous = $this->Election->find('first', array('conditions' => 
		                                            array('Election.username' => $username)));
        if($previous && count($previous) > 0) {
        	$this->set('results', "Sorry, you can only vote once in this election.");
            $this->render('sendvote', 'scratchr_userpage');
            return;
        }
		
		$vote = array(
			            'username' => $username,
			            'ip' => $this->RequestHandler->getClientIP()
			        );
	    
	    // valid post fields
        function mkvalid($n) { return "candidate$n"; }
        $valid = array_map(mkvalid, range(1,8));
        
        // add valid fields to the vote
        foreach ($_POST as $candidate => $votes) {
            if (in_array($candidate, $valid)) {
                $vote[$candidate] = intval($votes);
            }
        }
        
	    $this->Election->save($vote);
		$this->set('results', "Thank you for voting!");
		$this->render('sendvote', 'scratchr_userpage');
	}
	
	// Are we qualified to vote?
	function validatevote($user_id) {
	    $comment_count = $this->Pcomment->findCount(array('Pcomment.user_id' => $user_id,'comment_visibility'=>'visible')) 
	                    + $this->Gcomment->findCount(array('Gcomment.user_id' => $user_id,'comment_visibility'=>'visible'));

		$projects = $this->Project->find('all', array('conditions'=>array('Project.user_id' => $user_id, 'Project.proj_visibility'=>'visible'), 'fields'=> 'id'));
		
		$user = $this->User->find('first', array('conditions' => array('User.id = '.$user_id)));
		$day_diff = (strtotime('now')-strtotime($user['User']['created']))/86400;
		
        if ($this->User->find("User.status='locked' AND User.id = ".$user_id))
            return false; // the user is blocked

		// 30 days old & more than 2 projects & more than 10 comments
		if (($day_diff > 30) && ($comment_count > 10) && (count($projects) > 2))
		    return true; // valid vote
	    return false; // not good enough
	}
}
?>
