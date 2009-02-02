<?php
class ThanksController extends AppController {
	var $components = array('RequestHandler');
	var $uses =array('User','Thank');
	
	/*
	thank you machenism 
        */
        function thank_you($user_id) {
				$this->autoRender = false;
				$errors = Array();
                $this->exitOnInvalidArgCount(1);
				$client_ip = ip2long($this->RequestHandler->getClientIP());
                $session_user_id = $this->getLoggedInUserID();
                if (!$session_user_id)
                  // $this->__err();

                $client_ip = ip2long($this->RequestHandler->getClientIP());
				$this->User->id = $user_id;
        		$user = $this->User->read();
				$this->pageTitle = "Scratch |Thank you to "."'".$user['User']['username']."'";
				$similar_sender_count = $this->Thank->findCount("Thank.timestamp > now() - interval 1 day AND Thank.sender_id = $session_user_id AND Thank.reciever_id = $user_id");
				 
				$similar_sender_ip_count = $this->Thank->findCount("Thank.timestamp > now() - interval 1 day AND Thank.ipaddress = $client_ip AND Thank.sender_id = $session_user_id ");
				 if (!empty($this->params["form"]))
				 {
                	if(!empty($this->params["form"]['reason']))
					{
						$data['Thank']['sender_id'] = $session_user_id;
						$data['Thank']['reciever_id'] = $user_id;
						$data['Thank']['reason'] = $this->params['form']['reason'];
						$data['Thank']['ipaddress'] = $client_ip;
						if($similar_sender_count==0 && $similar_sender_ip_count==0)
						{
							if($this->Thank->save($data['Thank']))
							{
								$this->Session->setFlash(___("Thanks posted successfully.",true));
								$this->redirect('/thanks/thank_you/'.$user_id);
							}
						}
						else
						array_push($errors, ___('You can not post more than one thank you per day.',true));		
					}
					else
					array_push($errors, ___('Enter some reason.',true));	
				}
				
        if (empty($errors)) {
			$isError = false;
			} else {
			$isError = true;
		}
		$this->set('username',$user['User']['username']);
		$this->set('user_id',$user['User']['id']);
		$this->set('isError', $isError);
		$this->set('errors', $errors);
		$this->render('thank_you');
        }

}//class
?>