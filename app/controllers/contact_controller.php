<?php

class ContactController extends AppController {
	var $uses = array('Notification', 'Announcement');
	var $name = 'Contact';
	var $components = array('Email', 'RequestHandler');
	var $autoreplySubject = 'Autoreply from Scratch Website\: We received your message!';
	var $autoreplyBody = 'We\'ve received your message - Thanks!<br><br>We get a lot of email, so it may take a few days to get back to you. In the meantime, you can try looking for an answer to your question in the <a href="http://info.scratch.mit.edu/Support/Scratch_FAQ">Scratch FAQ</a>. You can also post questions that aren\'t related to specific accounts in the <a href="http://scratch.mit.edu/forums/">Scratch Forums</a>.<br><br>Scratch On!<br>Scratch Team';

	function us(){
		$this->pageTitle = ___('Scratch | Contact us', true);
#		$this->set('content_status', $this->getContentStatus());
		if(!$this->isLoggedIn())
		{
			$this->set('redirect_technicalquestion', '/login');
		}
		else
		{
			$this->set('redirect_technicalquestion', '/forums');
		}
		if (!empty($this->data))
		{
			$this->msg();
		} else {
			$this->render('us','scratchr_default');
		}
	}
	
	function us_banned() {
		$this->pageTitle = ___('Scratch | Blocked Account or IP address | Contact us', true);
#		$this->set('content_status', $this->getContentStatus());
		if (!empty($this->data))
		{
			$this->sendmsg();
		} else {
			$this->render('us_banned','scratchr_default');
		}
	}
	function sboard(){
		$this->pageTitle = ___('Scratch | Scratch Sensor Board | Contact Us', true);
		$this->set('content_status', $this->getContentStatus());
		if (!empty($this->data))
		{
			$this->msg('1', 'scratchboard@media.mit.edu');
		} else {
			$this->render('sboard','scratchr_default');
		}
	}

	function msg($nosubject=null, $mailto=null){
#		  $this->set('content_status', $this->getContentStatus());
		  $email = $this->data['Page']['email'];
		  $name = $this->data['Page']['name'];
		  $message = $this->data['Page']['message'];
		  $cc_topic = $this->data['Page']['cc_topic'];
		  $username = isset($this->data['Page']['username'])?$this->data['Page']['username']:null;
		  $this->set('selected_topic', $cc_topic);

		  $resp = recaptcha_check_answer (CAPTCHA_PRIVATEKEY,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

		  if($nosubject == '1') {
			$subject = 'Interested in Scratch Sensor Board';
		  } else{
			$subject = $this->data['Page']['subject'];
		  } 

		  if ($this->Email->validMail($email) == 0 )
		  {
			$this->set('error', ___("Invalid e-mail:", true) . " '$email'");
		  }
		  elseif(strlen(trim($subject)) < 4 )
		  {
			$this->set('error', ___("Subject is too short", true));
		  }
		  elseif(strlen(trim($message)) < 4 )
		  {
			$this->set('error', ___("Message is too short", true));
		  }
		  elseif(!$resp->is_valid)
		  {
			$this->set('error', ___("Authentication words were not entered correctly", true));
		  }
		  else{
			// append user information to message
			$ip=$this->RequestHandler->getClientIP();
			$message.="<BR>IP Address of user: ".$ip."\nHTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT'];
			if($this->isLoggedIn()){
				$username = $this->getLoggedInUsername();
			}
			if($username){
				$server = 'http://'.$_SERVER['HTTP_HOST'];
				$userlink = $server."/users/$username";
				$message .='<BR><a href ='.$userlink.'>'.$username.'</a>';
			}
			//here is the email sent
			$this->Email->email($email, $name, $message, $subject, $mailto, $email); 

			// Now send the sender a reply confirmation email
			// Leave last argument (mailfrom) empty so ScratchR loads its own info( see /app/controllers/components/email.php )
			$this->Email->email("<CONTACTUS_EMAIL>",'Scratch Team', $autoreplyBody, $autoreplySubject,$email,'' ); 
 
			// $this->Email->email($email, $name, $message, $subject, $cc_topic, $email);  //copy of the email sent to the person in charge of the topic selected
			$this->set('succes', ___('The message was sent', true) . " <br />" . ___('Thank you!', true));
	      }
	}
	
	function sendmsg($mailto=null){

		  $email = $this->data['Page']['email'];
		  $name = $this->data['Page']['name'];
		  $subject = $this->data['Page']['subject'];
		  $message = $this->data['Page']['message'];
		  $username = isset($this->data['Page']['username'])?$this->data['Page']['username']:null;
		
		  if ($this->Email->validMail($email) == 0 )
		  {
			$this->set('error', ___("Invalid e-mail:", true) . " '$email'");
		  }
		  elseif(strlen(trim($subject)) < 4 )
		  {
			$this->set('error', ___("Subject is too short", true));
		  }
		  elseif(strlen(trim($message)) < 4 )
		  {
			$this->set('error', ___("Message is too short", true));
		  }
		 
		  else{
			// append user information to message
			$ip=$this->RequestHandler->getClientIP();
			$message.="<BR>IP Address of user: ".$ip."\nHTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT'];

			if($this->isLoggedIn()){
				$username = $this->getLoggedInUsername();
			}
			if($username){
				$server = 'http://'.$_SERVER['HTTP_HOST'];
				$userlink = $server."/users/$username";
				$message .='<BR><a href ='.$userlink.'>'.$username.'</a>';
			}
			$this->Email->email($email, $name, $message, $subject, $mailto, $email);  //here is the email sent
			
			$this->set('succes', ___('The message was sent', true) . " <br />" . ___('Thank you!', true));
	      }
	}
}?>
