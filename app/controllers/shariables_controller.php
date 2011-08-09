<?php

class ShariablesController extends AppController {
	var $components = array('RequestHandler');
        var $helpers = array('Javascript', 'Ajax', 'Html');
        var $uses = array('Shariable', 'Notification', 'User');

	// ID-based delete function
	function delete_ajax($sid)
	{
		$this->autoRender=false;
		if($shariable_record = $this->Shariable->find("id = $sid"))
		{
			$suser_id = $shariable_record['Shariable']['user_id'];
			$user_record = $this->Session->read('User');
			$user_id = $user_record['id'];
			$isMine = $suser_id == $user_id;
			$isAdmin = $this->isAdmin();
			if($isMine || $isAdmin)
			{
				$this->Shariable->del($sid);
			}
		}
	}

	// URL-based read function
	function read($username, $sname)
	{
		$this->autoRender=false;
		if($user_record = $this->User->find("username = '$username'"))
		{
			$user_id = $user_record['User']['id'];
			if($shariable_record = $this->Shariable->find("user_id = $user_id AND name = '$sname'"))
			{
				echo $shariable_record['Shariable']['value'];
			}
		}
	}

	// ID-based write function
	function write_ajax($sid, $value=null)
	{
		$this->autoRender=false;
		if($shariable_record = $this->Shariable->find("id = $sid"))
		{
			$suser_id = $shariable_record['Shariable']['user_id'];
			$user_record = $this->Session->read('User');
			$user_id = $user_record['id'];
			$isMine = $suser_id == $user_id;
			$isAdmin = $this->isAdmin();
			if($isMine || $isAdmin)
			{
				$newValue=null;
				if($value!=null)
				{
					$newValue=$value;
				}
				else
				{
					$inputText = (isset($this->params['form']['value'])) ? $this->params['form']['value'] : null;
					if($inputText)
					{
						$newValue = htmlspecialchars($inputText);
					}
				}
				if($newValue!=null && isInappropriate($newValue))
				{
					$user_record = $this->Session->read('User');
					$user_id = $user_record['id'];
					$this->notify($user_id, 'We remind you to use appropriate language for all ages, please read the <a href="/terms">Community Guidelines</a>', false, false);
				}
				else
				{
					$this->Shariable->id = $sid;
					if($this->Shariable->saveField('value', $newValue))
					{
						echo $newValue;
					}
				}
			}
		}
	}

	// URL-based write function
	function write($username, $sname, $svalue)
	{
		$this->autoRender=false;
		$user_record = $this->User->find("username = '$username'");
		$user_id = $user_record['User']['id'];
		if($shariable_record = $this->Shariable->find("user_id = $user_id AND name='$sname'"))
		{
			$suser_id = $shariable_record['Shariable']['user_id'];
			$sid = $shariable_record['Shariable']['id'];
			$user_record = $this->Session->read('User');
			$user_id = $user_record['id'];
			$isMine = $suser_id == $user_id;
			$isAdmin = $this->isAdmin();
			if($isMine || $isAdmin)
			{
				$newValue=$svalue;
				if($newValue!=null && isInappropriate($newValue))
				{
					$user_record = $this->Session->read('User');
					$user_id = $user_record['id'];
					$this->notify($user_id, 'We remind you to use appropriate language for all ages, please read the <a href="/terms">Community Guidelines</a>', false, false);
				}
				else
				{
					$this->Shariable->id = $sid;
					if($this->Shariable->saveField('value', $newValue))
					{
						echo "Set Shariable '".$sname."' to '".$newValue."'";
					}
				}
			}
		}
	}

	function create($username, $sname = null, $svalue = null)
	{
		$this->autoRender=false;
		$new_sname = null;
		$new_svalue = null;
		if($sname!=null)
		{
			$new_sname = $sname;
		}
		else if($this->params['form']['shariable_name']!=null)
		{
			$new_sname = $this->params['form']['shariable_name'];
		}
		if($svalue!=null)
		{
			$new_svalue = $svalue;
		}
		else if($this->params['form']['shariable_value']!=null)
		{
			$new_svalue = $this->params['form']['shariable_value'];
		}
		if($new_sname!=null && $new_svalue!=null)
		{
			$user_record = $this->User->find("username = '$username'");
			$user_id = $user_record['User']['id'];
			if($shariable_record = $this->Shariable->find("user_id = $user_id AND name='$new_sname'"))
			{
				$suser_id = $shariable_record['Shariable']['user_id'];
				$sid = $shariable_record['Shariable']['id'];
				$user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$isMine = $suser_id == $user_id;
				$isAdmin = $this->isAdmin();
				if($isMine || $isAdmin)
				{
					$newValue=$new_svalue;
					if($newValue!=null && isInappropriate($newValue))
					{
						$user_record = $this->Session->read('User');
						$user_id = $user_record['id'];
						$this->notify($user_id, 'We remind you to use appropriate language for all ages, please read the <a href="/terms">Community Guidelines</a>', false, false);
					}
					else
					{
						$this->Shariable->id = $sid;
						$this->Shariable->saveField('value', $newValue);
					}
				}
			}
			else
			{
				$user_record = $this->Session->read('User');
				$session_username = $user_record['username'];
				$user_record = $this->User->find("username = '$username'");
				$user_id = $user_record['User']['id'];

				$isMe = $session_username == $username;
				$isAdmin = $this->isAdmin();
				if($isMe || $isAdmin)
				{
					$shariables_used = $this->Shariable->findCount("user_id = $user_id");
					$shariables_left = MAX_SHARIABLES - $shariables_used;
					if($shariables_left>0)
					{
						$data['Shariable']['user_id']=$user_id;
						$data['Shariable']['type']="numeric"; //default
						$data['Shariable']['name']=$new_sname;
						$data['Shariable']['value']=$new_svalue;
						$this->Shariable->save($data);
					}
				}
			}
		}
		$this->redirect('users/'.$username);
	}

}
?>
