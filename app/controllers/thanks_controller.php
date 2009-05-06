<?php
class ThanksController extends AppController {
 	var $components = array('RequestHandler','Pagination', 'Email');
    var $helpers = array('Javascript', 'Ajax', 'Html', 'Pagination', 'Template');
	
	var $uses =array('User','Thank');
	
	/*
	thank you machenism 
        */
        function give($user_id) {
				$this->autoRender = false;
				
                $this->exitOnInvalidArgCount(1);
				$client_ip = ip2long($this->RequestHandler->getClientIP());
                $session_user_id = $this->getLoggedInUserID();
                $client_ip = ip2long($this->RequestHandler->getClientIP());
				$users = $this->User->find("User.id =$user_id",'id,username');
				$isMe = $this->activeSession($user_id);
				$data['Thank']['sender_id'] = $session_user_id;
				$data['Thank']['reciever_id'] = $user_id;
				$data['Thank']['reason'] = $this->params['form']['reason'];
				$data['Thank']['ipaddress'] = $client_ip;
				$this->Thank->save($data['Thank']);
				$this->set('just_thanked', true);
				$this->set('user_id',$user_id);
				$this->set('isMe',$isMe);
				$this->set('thanks_to_username',$users['User']['username']);
				$this->render("thanks_ajax", "ajax");
				return;
				 
        }
		
		function show($user_id)
		{
			$session_user_id = $this->getLoggedInUserID();
			if($session_user_id !=$user_id)
			$this->cakeError('error404');
			$this->pageTitle = "Scratch | Thank you";
			$this->Pagination->show = 20;
			$this->modelClass = "Thank";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Thank", 
						"direction"=> "DESC", "url"=>"/thanks/show/$user_id/" );
			list($order,$limit,$page) = $this->Pagination->init("Thank.reciever_id = $user_id ", Array(), $options);
			$data = $this->Thank->findAll("Thank.reciever_id = $user_id ", null, $order, $limit, $page);
			$this->set('data',$data);
		}//function
		
		function  delete_thanks($thank_id,$user_id){
		$session_user_id = $this->getLoggedInUserID();
		if($session_user_id !=$user_id)
		$this->cakeError('error404');
		$this->Thank->del($thank_id);
		exit;
		
		}

}//class
?>