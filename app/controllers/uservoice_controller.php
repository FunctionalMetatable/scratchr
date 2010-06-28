<?php
class UservoiceController extends AppController {
   
   var $name = 'uservoice';
   var $uses = array("Project","User","Pcomment","Gcomment");
   
   // redirects the user to the page on uservoice with their login info
   // if the user is an experienced user, or to a page explaining why
   // they can't if it is a new scratcher.
   function index() {
    $account_key = "scratch";
    $api_key = "fc52e139e19cdb76957c674fa2a9f609";
    $salted = $api_key . $account_key;
    $hash = hash('sha1',$salted,true);
    $saltedHash = substr($hash,0,16);
    $iv = "OpenSSL for Ruby";
    
    $username = $this->getLoggedInUsername();
	$user_id = $this->getLoggedInUserID();

    // Decide whether to send user data and login to uservoice, or send user to sorry can't vote page.
	// Eventually this should be done on basis of group membership (New Scratcher or Scratcher). 
	// For now, let's make criteria:
	// > 10 comments && > 2 projects && account age > 30 days.
	
	$canSuggest = true;
	$comment_count = $this->Pcomment->findCount(array('Pcomment.user_id' => $user_id,'comment_visibility'=>'visible')) + $this->Gcomment->findCount(array('Gcomment.user_id' => $user_id,'comment_visibility'=>'visible'));
	if ($comment_count < 10)
	$canSuggest = false;
	$projects = $this->Project->find('all', array('conditions'=>array('Project.user_id' => $user_id, 'Project.proj_visibility'=>'visible'), 'fields'=> 'id', 'recursive'=> -1, 'order' =>'created DESC'));
	if (count($projects) < 2)
	$canSuggest = false;
	$user = $this->User->read();
	$created = preg_split("/[-\s:]+/",$user['created']);
	$created = mktime($created[3],$created[4],$created[5],$created[1],$created[2],$created[0]);
	$day_diff = (time() - $created)/86400;
	if ($day_diff <30)
	$canSuggest = false;
	if (!$canSuggest)
    $this->redirect('http://info.scratch.mit.edu/Suggestions_New_Scratcher');

    // End test

    $user_data = array(
	    "guid" => $this->$user_id,
        "expires" => date('Y-m-d', strtotime('+1 year')),
        "display_name" => $username,
        "url" => 'http://scratch.mit.edu/users/'.$this->getLoggedInUrlname(),
        "avatar_url" => 'http://scratch.mit.edu/static/icons/buddy/'.$this->getLoggedInuser_id().'_med.png'
      );

      if ($this->isAdmin())
         $user_data['admin'] = 'accept';
      else
         $user_data['admin'] = 'deny';

      $data = json_encode($user_data);

      // double XOR first block
      for ($i = 0; $i < 16; $i++)
      {
        $data[$i] = $data[$i] ^ $iv[$i];
      }
      
      $pad = 16 - (strlen($data) % 16);
      $data = $data . str_repeat(chr($pad), $pad);
	
      $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
      mcrypt_generic_init($cipher, $saltedHash, $iv);
      $encryptedData = mcrypt_generic($cipher,$data);
      mcrypt_generic_deinit($cipher);

      $encryptedData = urlencode(base64_encode($encryptedData));

      $this -> redirect ("http://scratch.uservoice.com?sso=".$encryptedData);
      }
   }
?>
