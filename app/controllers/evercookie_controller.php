<?php

/*
 * Evercookie Controller:  implements evercookie checking
 * as a method to prevent banned-users from registering
 * 
 * Tim Mickel - UROP 2011
 */

class EvercookieController extends AppController
{
	var $name = 'Evercookie';
	var $uses = array("User");
	var $components = array("Email");
	
	// Both of these methods are actually obsolete unless we implement hybrid checking
	// Right now, evercookie checking is handled in signup.thtml
	// and warning emails are handled in users_controller.php
	// Evercookie setting still takes place in us_banned.thtml
	
	function checkCookie()
	{
		/* Render the evercookie checking page.
		 * This will check the cookie and either block the user
		 * from registering or redirect them to the sign up page.
		 * **** DISABLED AT THIS TIME ****
		 */
		$this->set("title_for_layout","Confirming sign up");
		$this->set("title", "Confirming sign up");
		$this->render("evercookie.thtml");
	}
	
	function checkBan($username)
	{
		/*
		* After the user's evercookie is detected, they will be directed here (from checkCookie).
		* We check if a ban actually exists, and if it does, email caution.
		* Then we allow them to sign up...
		*/
		
		$userdata = $this->User->getUserByScreenName($username);
		
		if($userdata['User']['status'] == "locked")
		{
			// The user is banned
			
			$subject = "Banned account registration attempt ($username)";
			
			$msg = "The user $username, who has an Evercookie installed on their computer, has attempted to register an account
			on Scratch with the IP address " . $this->RequestHandler->getClientIP() . ".";
						
			$this->Email->email('caution','Scratch Website', $msg, $subject, 'caution@scratch.mit.edu');
						
			// Change this to render a ban page in the future
			$this->redirect('/multiaccountwarn?ban');
		}
		else
		{
			// The ban must have been lifted
			// change this to an evercookie-clearing page in the future?
			$this->redirect('/multiaccountwarn?clear');
		}
	}
}

?>
