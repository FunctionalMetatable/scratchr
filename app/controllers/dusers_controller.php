<?php
class DusersController extends AppController
{

	# This controller handles registration of users who download scratch
	# at some point it will be merged with the registration to scratchr
	#
	var $name = 'Dusers';

	function mlist()
	{
		$this->set('content_status', $this->getContentStatus());
		if ($this->isLoggedIn()) {
			$this->autoLayout = false;
			$this->set('dusers', $this->Duser->findAll());
		} else{
			$this->redirect("/");
		}
	}


	
	function add()
	{
	// This function is pointed to by 'scratch.mit.edu/download' on the current site.
	// Due to a lack of understanding on my part of how to make the main site accept changes
	// to the config/routes.php, the current solution taken is to replace the functionality here
	// with that of the new function that we want to use at 'scratch.mit.edu/redirect/download
	$this->redirect("/redirect/download");
	/*$this->set('content_status', $this->getContentStatus());
	$this->pageTitle = ___("Scratch Download Form", true);
	//$this->autoLayout = false;
	if (!empty($this->data['Duser']))
	{
		if (empty($this->data['Duser']['role']))
		{
			if ($this->data['Duser']['role-student'])
			{
				$this->data['Duser']['role'] .= "student, ";
			}
			if($this->data['Duser']['role-educator'])
			{
				$this->data['Duser']['role'] .= "educator, ";
			}
			if($this->data['Duser']['role-parent'])
			{
				$this->data['Duser']['role'] .= "parent, ";
			}
			if($this->data['Duser']['role-researcher'])
			{
				$this->data['Duser']['role'] .= "researcher, ";
			}
		}
	if  (empty($this->data['Duser']['firstname'])){
           $this->data['Duser']['firstname'] = 'Anonymous';
       }
       if  (empty($this->data['Duser']['lastname']))  {
           $this->data['Duser']['lastname'] = 'Anonymous';
       }
       if  (empty($this->data['Duser']['email']))   {
           $this->data['Duser']['email'] = 'anonymous@localhost.org';
       }
       if  (empty($this->data['Duser']['organization'])) {
           $this->data['Duser']['organization'] = 'anonymous';
       }
       
           if($this->Duser->save($this->data['Duser']))
           {
            $this->redirect("/pages/download");
           }
       }
	   */
	}

}
?>