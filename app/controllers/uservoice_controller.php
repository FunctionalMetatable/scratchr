<?php
Class UservoiceController extends AppControler {
   
   var $uses = array("Project","RelationshipType", "Relationship", "Gallery", "GalleryMembership", "User", "FriendRequest", "Notification");
   
   // redirects the user to the page on uservoice with their login info
   // if the user is an experienced user, or to a page explaining why
   // they can’t if it is a new scratcher.
   function index() {
      $account_key = "scratch";
      $api_key = "fc52e139e19cdb76957c674fa2a9f609";
      
      $salted = $api_key . $account_key;
      $hash = hash('sha1',$salted,true);
      $saltedHash = substr($hash,0,16);
      $iv = "OpenSSL for Ruby";
      
      // Testing if user has made projects

      $username = $this->getLoggedInUsername()
      
      $ch = curl_init('http://scratch.mit.edu/api/getprojectsbyusername/'.$username);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $has_projects = curl_exec($ch);
      
      curl_close($ch);


      if (!trim($has_projects))
      $this->redirect(“http http://info.scratch.mit.edu/Suggestions_New_Scratcher");

      // End test

      $user_data = array(
        "guid" => $this->getLoggedInUserID(),
        "expires" => date('Y-m-d', strtotime('+1 year')),
        "display_name" => $username,
        "url" => 'http://scratch.mit.edu/users/'.$this->getLoggedInUrlname(),
        "avatar_url" => 'http://scratch.mit.edu/static/icons/buddy/'.$infoArr['guid'].'_med.png'
      );


      if $this->isAdmin():
         $user_data['admin'] = 'accept';
      else:
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
