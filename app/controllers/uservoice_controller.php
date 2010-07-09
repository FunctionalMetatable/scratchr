<?php
class UservoiceController extends AppController {
   
	var $name = 'uservoice';
	var $uses = array("Project","User","Pcomment","Gcomment");
   
	// redirects the user to the page on uservoice with their login info
	// if the user is qualified, or to a page explaining why
	// they can't if it is a new scratcher.
	function index() 
	{
		$username = $this->getLoggedInUsername();
		// If the user is not logged in, bail to suggestions not enough privs page. Otherwise queries will fail.
		if(!$username)
		{
			$this->Session->write('uservoiceRedirect', 'uservoice');
			$this->Session->write('uservoiceRedirectTime', time());
			$this->redirect('/login');
			return;
		}

		$account_key = "scratch";
		$api_key = "fc52e139e19cdb76957c674fa2a9f609";
		$salted = $api_key . $account_key;
		$hash = hash('sha1',$salted,true);
		$saltedHash = substr($hash,0,16);
		$iv = "OpenSSL for Ruby";
    
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
			echo 'json package needed';
			
			$user_data = array(
		    	"guid" => $user_id,
			"expires" => date('Y-m-d G:i:s', strtotime('+1 day')),
			"display_name" => $username,
			"url" => 'http://scratch.mit.edu/users/'.$this->getLoggedInUrlname(),
			"avatar_url" => 'http://scratch.mit.edu/static/icons/buddy/'.$user_id.'_med.png'
	      		);

	      		if ($this->isAdmin()){
		 		$user_data['admin'] = 'accept';
				$user_data['email'] = $this->Session->read('User.email');
			}
	      		else
		 		$user_data['admin'] = 'deny';

	      		$data = json_encode($user_data);
			echo '<br>Never mind. It seems that you have that. Now I need the mcrypt package';

	      		// double XOR first block
	      		for ($i = 0; $i < 16; $i++)
	      		{
				$data[$i] = $data[$i] ^ $iv[$i];
	      		}
	     		echo 'for';
			$pad = 16 - (strlen($data) % 16);
			$data = $data . str_repeat(chr($pad), $pad);
			echo 'before';
			$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
			echo 'after';
			mcrypt_generic_init($cipher, $saltedHash, $iv);
			echo 'later';
			$encryptedData = mcrypt_generic($cipher,$data);
			mcrypt_generic_deinit($cipher);

			$encryptedData = urlencode(base64_encode($encryptedData));
			echo 'almost';
			$this -> redirect ("http://suggest.scratch.mit.edu?sso=".$encryptedData);
		}
		// If the user is ineligible to make suggestions, redirect them to a page that says why.
		else
		{
			$this->redirect('http://info.scratch.mit.edu/Suggestions_New_Scratcher');
		}
	}
	
	function test(){
		echo $user_id = $this->getLoggedInUserID();
		echo '<br>';
		$user = $this->User->find('first', array('conditions' => array('User.id = '.$user_id)));
		echo print_r($user);
		echo '<br><br>';
		echo '<br>';
		echo $user['User']['created'];
		echo '<br>';
		echo $day_diff = (strtotime('now')-strtotime($user['User']['created']))/86400;
	}
	

	function logout(){
	echo'<meta http-equiv="refresh" content="0;url=/logout"/>
	<script type="text/javascript" src="http://scratch.uservoice.com/logout.json"></script>';
	$this->autoRender = false;
	}
}
?>
