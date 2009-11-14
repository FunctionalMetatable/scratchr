<?php

class ContactController extends AppController {
	var $uses = array('Notification', 'Announcement');
	var $name = 'Contact';
	var $components = array('Email', 'RequestHandler');

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
			$message.="\n\nIP Address of user: ".$ip."\nHTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT'];

			$this->Email->email($email, $name, $message, $subject, $mailto, $email);  //here is the email sent
			$this->Email->email($email, $name, $message, $subject, $cc_topic, $email);  //copy of the email sent to the person in charge of the topic selected
			$this->set('succes', ___('The message was sent', true) . " <br />" . ___('Thank you!', true));
	      }
	}
	
	function sendmsg($mailto=null){

		  $email = $this->data['Page']['email'];
		  $name = $this->data['Page']['name'];
		  $subject = $this->data['Page']['subject'];
		  $message = $this->data['Page']['message'];
		
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
			$message.="\n\nIP Address of user: ".$ip."\nHTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT'];

			$this->Email->email($email, $name, $message, $subject, $mailto, $email);  //here is the email sent
			
			$this->set('succes', ___('The message was sent', true) . " <br />" . ___('Thank you!', true));
	      }
	}
}?>
