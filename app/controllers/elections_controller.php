<?php
class ElectionsController extends AppController {
   
	var $name = 'elections';
	var $uses = array("Project","User","Pcomment","Gcomment");
   
	// redirects the user to the page on uservoice with their login info
	// if the user is qualified, or to a page explaining why
	// they can't if it is a new scratcher.
	function vote() 
	{
		$username = $this->getLoggedInUsername();
		// If the user is not logged in, bail to suggestions not enough privs page. Otherwise queries will fail.
		if(!$username)
		{
			$this->Session->write('uservoiceRedirect', 'moderationelections');
			$this->Session->write('uservoiceRedirectTime', time());
			$this->redirect('/login');
			return;
		}

		$user_id = $this->getLoggedInUserID();

		// Gather data to evaluate if user is qualified to make suggestions via uservoice. 	
		$comment_count = $this->Pcomment->findCount(array('Pcomment.user_id' => $user_id,'comment_visibility'=>'visible')) + $this->Gcomment->findCount(array('Gcomment.user_id' => $user_id,'comment_visibility'=>'visible'));

		$projects = $this->Project->find('all', array('conditions'=>array('Project.user_id' => $user_id, 'Project.proj_visibility'=>'visible'), 'fields'=> 'id', 'recursive'=> -1, 'order' =>'created DESC'));
		
		$user = $this->User->find('first', array('conditions' => array('User.id = '.$user_id)));
		$day_diff = (strtotime('now')-strtotime($user['User']['created']))/86400;
		
        	// checks to see if the user is currently blocked from the site.
                if( $this->User->find("User.status='locked' AND User.id = ".$user_id)) {
                    $this->redirect('/users/us_banned/'.$user_blocked);
		    exit;
                }


		// For now, let's use the following criteria to see if the account is eligible
		// 30 days old & more than 2 projects & more than 10 comments // (count($projects) > 2) &&
		if (($day_diff >30) && ($comment_count > 10) && (count($projects) > 2))
		{    	
			//$username = $user['User']['username'];
			$this->set('user', $username);
			$this->render('vote', 'scratchr_userpage');
		}
		else {
			$this->redirect('http://info.scratch.mit.edu/Community_Moderator_Election_New_Scratcher');
		}
	}

	function sendvote() 
	{
	      $candidates = array('candidate1', 'candidate2', 'candidate3', 'candidate4', 'candidate5', 'candidate6', 'candidate7', 'candidate8');
	      $duplicates = false;
	      for ($i = 1; $i < 8; $i++){
			for ($j = $i + 1; $j <= 8; $j++){
				if ($_POST[$candidates[$i]] == $_POST[$candidates[$j]] && $_POST[$candidates[$i]] != 0){
					    $errors = array();
					    $duplicates = true;
					    $errors['column'] = "Please only choose one candidate per column";
					    $this->set('errors', $errors);
					    $this->render('vote', 'scratchr_userpage');
					    break;
				}
			}
			if ($duplicates){
				break;
			}
	      }
	      if (!$duplicates){
			$postdata = array();
			foreach ($candidates as $candidate){
				$postdata[$candidate] = $_POST[$candidate];
			}
			$postdata['username'] = $this->getLoggedInUsername();
			$postdata['ip'] = $this->Session->read('User.ipaddress');
			App::import('Core', 'HttpSocket');
			$HttpSocket = new HttpSocket();
			$results = $HttpSocket->post('http://chinuas.scripts.mit.edu/php/elections.php', $postdata);
			$this->set('results', $results);
			$this->render('sendvote', 'scratchr_userpage');
	      }
	}
}
?>
