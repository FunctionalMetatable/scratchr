<?php
 class AdministrationController extends AppController {
    var $name = 'Administration';
   
    var $uses = array('AdminTag', 'KarmaSetting', 'KarmaRating', 'KarmaEvent', 'KarmaRank', 'Mgcomment', 'RemixedProject', 'GalleryMembership', 
	'BlockedUser', 'ViewStat', 'Gcomment', 'BlockedIp', 'ProjectFlag', 'Announcement', 'AdminComment', 'Apcomment', 'Mpcomment', 'Project', 
	'FeaturedGallery', 'ClubbedGallery', 'Pcomment', 'User', 'Gallery', 'Tag', 'Flagger', 'Downloader', 'Favorite', 'Lover', 'Notification',
	'Permission','PermissionUser', 'BlockedUserFrontpage','WhitelistedIpAddress');
    var $components = array('RequestHandler','Pagination', 'Email');
    var $helpers = array('Javascript', 'Ajax', 'Html', 'Pagination', 'Template');

    /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
   function beforeFilter() {
		$user_id = $this->getLoggedInUserID();
		$users_permission =$this->isAnyPermission();
		$allowed =array('ban_user','add_banned_user','render_banned_users','remove_banned_user','set_banned_users','ban_ip','expand_ip','render_ips','add_ban_ip','remove_ban_ip','index');
		if (($this->isAdmin() || isset($users_permission['block_IP']) || isset($users_permission['block_account'])))
		{}
		else
		{
			$this->cakeError('error404');
		}
		
		if(in_array($this->action,$allowed) || $this->isAdmin())
		{}
		else
		{
			$this->cakeError('error404');
		}
    }
    
    function __err() {
        $this->render('aerror');
        
    }
    
    function index() { 

    }
    
    function projects() { 	
    	$this->redirect('/administration/'.'psort/' . 'created');
    }
	
	/*************Set Permission*****************/
	function set_permission($user_id=null)
	{
	
		$this->autoRender = false;
		$permissions = $this->Permission->find('list');
		$this->set('permissions',$permissions);
		$this->User->bindPermission(); 

		$this->User->id=$user_id;
		$user=$this->User->read();
		if(empty($user)){
		$this->cakeError('error404');
		}
		$this->set('user', $user);
		
		$current_permissions=array();
		foreach($user['Permission'] as $permission)
		{
		array_push($current_permissions, $permission['id']);
		}
		$this->set('current_permissions',$current_permissions);
		$this->set('user_id',$user_id);
		$this->set('isError', false);
		$this->render('set_permission');
	}
	
	function add_set_permission($user_id=null)
	{
		$this->autoRender = false;
		$errors = Array();
		$this->PermissionUser->deleteAll('PermissionUser.user_id='.$user_id);
		if(!empty($this->data['Permission']['Permission']))
		{
			
			foreach($this->data['Permission']['Permission'] as $permission)
			{
				$data['PermissionUser']['user_id']=$user_id;
				$data['PermissionUser']['permission_id']=$permission;
				$permission_record = $this->PermissionUser->find(array('PermissionUser.permission_id' => $permission,'user_id'=>$user_id));
				if (empty($permission_record)) 
				{
					$this->PermissionUser->save($data['PermissionUser']);
					$this->PermissionUser->id=false;
				}
				$this->Session->setFlash(___("Permissions set successfully.",true));
				
			}//foreach
		}//this->data
		else
		{
			array_push($errors, ___('Please Select permission to set.',true));
		}
		
		$this->User->bindPermission();
		$users_permissions = $this->User->find('id='.$user_id);
		$this->set('data',$users_permissions);
		$this->set('user_id',$user_id);
		if (empty($errors)) {
			$isError = false;
		} else {
			$isError = true;
		}
		$this->set('isError', $isError);
		$this->set('errors', $errors);
		$this->render('render_users_permission_list_ajax', 'ajax');
	}
	
	function remove_users_permission($permission_id=null,$user_id=null)
	{
		$this->autoRender = false;
		$this->PermissionUser->del($permission_id);
		$this->User->bindPermission();
		$users_permissions = $this->User->find('id='.$user_id);
		$this->set('data',$users_permissions);
		$this->set('user_id',$user_id);
		$this->set('isError', false);
		$this->render('render_users_permission_list_ajax', 'ajax');
	}
	
	/****************End Set Permission************/
	
	function search_ip(){
	 $blocked_record = array();
	 $ip = null;
	 $is_banned = false;
		if(!empty($this->params['form'])){ 
		 $ip =  $this->params['form']['admin_search_ip_textarea'];
		//check if any blocked user used this ip in last one month
			
			 $locked_id = $this->checkLockedUser($ip); 
			 if($locked_id){
			 	$is_banned =true;
			 	$blocked_record =$this->User->find("User.id = $locked_id AND User.status='locked'");
			 }
		}
		$this->set('results', $blocked_record);
		$this->set('ip', $ip);
		$this->set('is_banned',$is_banned);
	}
	
	
	
	/******************DB Repair********************/
	/******************DO NOT RUN THESE UNLESS YOU KNOW YOU ARE DOING***********************/
	
	function repair_banlist() {
		$this->autoRender = false;
		$banned_users = $this->User->findAll("User.status = 'locked'");
		foreach ($banned_users as $user) {
			$default_reason = "You have violated our Terms of Use. ";
			$current_user_id = $user['User']['id'];
			$ban_record = $this->BlockedUser->find("BlockedUser.user_id = $current_user_id");
			if (empty($ban_record)) {
				$info = Array('BlockedUser' => Array('id' => null, 'user_id' => $current_user_id, 'admin_id' => 139, 'reason' => $default_reason));
				$this->BlockedUser->save($info);
			}
		}
	}
	
	function repair_featuredgalleries() {
		set_time_limit(9999999); 
		ini_set('memory_limit','500M');
		
		$featured_galleries = $this->FeaturedGallery->findAll();
		foreach ($featured_galleries as $gallery) {
			$id = $gallery['FeaturedGallery']['id'];
			$gallery_id = $gallery['FeaturedGallery']['gallery_id'];
			$target = $this->Gallery->find("Gallery.id = $gallery_id");
			
			if (empty($target)) {
				$this->FeaturedGallery->del($id);
			}
		}
	}
	
	function repair_gallerymembership($owner_id, $gallery_id) {
		$membership_count = $this->GalleryMembership->findCount("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.type = 0 AND GalleryMembership.user_id = $owner_id");
		if ($membership_count == 0) {
			$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $owner_id, 'gallery_id' => $gallery_id, 'type' => 0, 'rank' => 'owner'));
			$this->GalleryMembership->save($info);
		}
		
		$extra_membership = $this->GalleryMembership->findAll("GalleryMembership.type = 0 AND GalleryMembership.gallery_id = $gallery_id");
		foreach ($extra_membership as $current_membership) {
			$current_membership_id = $current_membership['GalleryMembership']['id'];
			$current_membership_user = $current_membership['GalleryMembership']['user_id'];
			if ($owner_id != $current_membership_user) {
				$this->GalleryMembership->del($current_membership_id);
			}
		}
		$redundant_memberships = $this->GalleryMembership->findAll("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.type != 0 AND GalleryMembership.user_id = $owner_id");
		foreach ($redundant_memberships as $current_membership) {
			$current_membership_id = $current_membership['GalleryMembership']['id'];
			$this->GalleryMembership->del($current_membership_id);
		}
	}
	
	function repair_galleries() {
		set_time_limit(9999999); 
		ini_set('memory_limit','500M');
		$this->autoRender = false;
		
		$galleries = $this->Gallery->findAll();
		foreach($galleries as $gallery) {
			$gallery_id = $gallery['Gallery']['id'];
			$owner_id = $gallery['User']['id'];
			if ($owner_id == null || $owner_id == 0) {
				$this->Gallery->del($gallery_id);
			} else {
				$owner_record = $this->User->find("User.id = $owner_id");
				if (empty($owner_record)) {
					//$this->Gallery->del($gallery_id);
				} else {
					$this->repair_gallerymembership($owner_id, $gallery_id);
				}
			}
		}
	}
	
	function repair_membership() {
		set_time_limit(9999999); 
		ini_set('memory_limit','500M');
		$this->autoRender = false;
		
		$gallery_memberships = $this->GalleryMembership->findAll();
		foreach($gallery_memberships as $membership) {
			$membership_id = $membership['GalleryMembership']['id'];
			$gallery_id = $membership['GalleryMembership']['gallery_id'];
			$gallery = $this->Gallery->find("Gallery.id = $gallery_id");
			if (empty($gallery)) {
				$this->GalleryMembership->del($membership_id);
			}
		}
	}
	
	function repair_projects($order = 0) {
		set_time_limit(9999999); 
		ini_set('memory_limit','1000M');
		$this->autoRender = false;
		$start_id = $order * 5000;
		$end_id = $order * 5000 + 5000;
		$projects = $this->Project->findAll("Project.id >= $start_id AND Project.id <= $end_id");
		
		foreach ($projects as $project) {
			$project_id = $project['Project']['id'];
			$owner_id = $project['Project']['user_id'];
			$owner_count = $this->User->findCount("User.id = $owner_id");
			$this->Project->id=$project_id;
			$current_project = $this->Project->read();
			if ($owner_count == 0) {
				$this->Project->saveField("proj_visibility", "delbyusr");
			}
			$remix_count = $this->RemixedProject->findCount("RemixedProject.oproject_id = $project_id");
			$this->Project->saveField("remixes", $remix_count);
		}
	}
	
	function repair_gcomments() {
		set_time_limit(9999999); 
		ini_set('memory_limit','1000M');
		$this->autoRender = false;
		
		$gcomments = $this->Gcomment->findAll();
		foreach ($gcomments as $comment) {
			$comment = $comment['Gcomment']['content'];
			$comment_id = $comment['Gcomment']['id'];
			$comment_length = strlen($comment);
			$visibility = $comment['Gcomment']['visibility'];
			$this->Gcomment->id = $comment_id;
			$current_comment = $this->Gcomment->read();
			if ($visibility == 0) {
				$this->Project->saveField("comment_visibility", "delbyadmin");
			} else {
				$this->Project->saveField("comment_visibility", "visible");
			}
		}
	}
	
	function repair_pcomments() {
		set_time_limit(9999999); 
		ini_set('memory_limit','1000M');
		$this->autoRender = false;
		
		$pcomments = $this->Pcomment->findAll();
		foreach ($pcomments as $comment) {
			$comment = $comment['Pcomment']['content'];
			$comment_id = $comment['Pcomment']['id'];
			$comment_length = strlen($comment);
			$visibility = $comment['Pcomment']['visibility'];
			$this->Pcomment->id = $comment_id;
			$current_comment = $this->Pcomment->read();
			if ($visibility == 0) {
				$this->Project->saveField("comment_visibility", "delbyadmin");
			} else {
				$this->Project->saveField("comment_visibility", "visible");
			}
		}
	}
	/***
	/	Projects ---------------------------------------------------------------------------------------------------------
	***/
	
	/**
	/	Shows the details of the project
	/	$project_id => id of target project
	/	$option => sorting options
	**/
    function viewproject($project_id, $option = null) {
    	$this->modelClass = "Project";
    	$this->Project->id = $project_id;
		$this->Project->bindUser();
		$project = $this->Project->read();
		if(empty($project))
			$this->cakeError('error404');
		$project_flags = $this->ProjectFlag->find("project_id = $project_id");
		$admin_id = $project_flags['ProjectFlag']['admin_id'];
		if ($admin_id == null) {
			$admin_name = "NONE";
		} else {
			$admin_user = $this->User->find("id = $admin_id");
			if ($admin_user == null) {
				$admin_name = "NONE";
			} else {
				$admin_name = $admin_user['User']['username'];
			}
		}


		if ($option == "comments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUserComments/" . $project_id);
			list($order,$limit,$page) = $this->Pagination->init("project_id = $project_id", Array(), $options);
			$data = $this->Pcomment->findAll("project_id = $project_id", null, $order, $limit, $page);
			
			$this->set('data', $data);
		}
		if ($option == "dcomments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUserComments/" . $project_id);
			list($order,$limit,$page) = $this->Pagination->init("project_id = $project_id AND visibility = 0", Array(), $options);
			$data = $this->Pcomment->findAll("project_id = $project_id AND visibility = 0", null, $order, $limit, $page);
			
			$this->set('data', $data);
		}
		if ($option == "flaggers") {
			$this->Flagger->bindUser();
			$this->set('data', $this->Flagger->findAll("project_id = $project_id", null, "Flagger.timestamp DESC"));
		}
		if ($option == "lovers") {
			$this->Lover->bindUser();
			$this->set('data', $this->Lover->findAll("project_id = $project_id", null, "Lover.timestamp DESC"));
		}
		if ($option == "favoriters") {
			$this->Favorite->bindUser();
			$this->set('data', $this->Favorite->findAll("project_id = $project_id", null, "Favorite.timestamp DESC"));
		}
		if ($option == "downloaders") {
			$this->Downloader->bindUser();
			$this->set('data', $this->Downloader->findAll("project_id = $project_id", null, "Downloader.timestamp DESC"));
		}
		if ($option == "administration") {
			$data = $this->Apcomment->findAll("project_id = $project_id");
			$this->set('data', $data);
		}
		
		$this->set('project', $project);
		$this->set('option', $option);
		$this->set('reviewer_name', $admin_name);
		$this->set('project_id', $project_id);
		
    }
    
	/**
	/	Orders projects by option criteria + project name filter
	/	$options = sorting options
	/	$results = TODO
	**/
    function psort($options = null, $results = null) {
    	$this->autoRender = false;
    	$this->modelClass = "Project";
    	$this->Project->bindUser();
    	list($order,$limit,$page) = $this->Pagination->init();
    	$order = "Project.created DESC";
    	$this->set('option', $options);
    	
    	if ($options == "created") {
    		$order = "Project.created DESC";
    	}
    	if ($options == "creator") {
    		$order = "User.username ASC";
    	}
    	if ($options == "flags") {
    		$order = "Project.flagit DESC";
    	}
    	if ($options == "loves") {
    		$order = "Project.loveit DESC";
    	}
    	if ($options == "name") {
    		$order = "Project.name ASC";
    	}
    	
		//Checking for keywords within filter, if so find projects using key, otherwise render normally
    	if (!empty($this->data['Filter'])) {
    		$paginate = array('limit' => 10, 'page' => 1); 
    		$results = array();
    		$counter = 0;
    		$key = $this->data['Filter']['key'];
    		$haystack = $this->Project->findAll("Project.name = '$key'", NULL, $order,$limit,$page);
    		$this->set('data', $haystack);
    		$this->render('projects');
    	} else {
    		$this->set('data', $this->Project->findAll(NULL, NULL, $order, $limit, $page));
    		$this->render('projects');
    	}
    }
    
	/**
	/	Change project safe level
	/ 	$safe_level - see database project.safe
	**/
	function set_safe($project_id, $safe_level) {
		$this->Project->id = $project_id;
		$this->Project->bindUser();
		$project = $this->Project->read();
		if(empty($project))
		$this->cakeError('error404');
		$username = $project['User']['username'];
		$this->Project->saveField("safe", $safe_level);
		$this->redirect('/projects/'. $username. '/' . $project_id);
	}
	
	/**
	/	Deletes a project
	/	$project_id
	**/
    function deleteproject($project_id) {
    	$this->hide_project($project_id, "delbyadmin");
		$this->redirect('/administration/'.'projects');
    }
    
	/**
	/	Deletes a comment under Section: Projects
	/	$project_id
	/	$comment_id
	/	$option - not used locally, needs to be passed
	**/
    function deletecomment($project_id, $comment_id, $option = null) {
    	$this->Pcomment->id = $comment_id;
    	$this->Pcomment->read();
    	$this->Pcomment->saveField("comment_visibility", "delbyadmin") ;
    	$this->set('option', $option);
    	$this->set('data', $this->Pcomment->findAll("project_id = $project_id", null, "Pcomment.timestamp DESC"));
    	$this->set('project_id', $project_id);
    	$this->setFlash("Comment deleted", FLASH_NOTICE_KEY);
    	$this->render('admincomments_ajax', 'ajax');
    }
    
	/**
	/	Deletes a comment under Section: Projects
	/	$project_id
	/	$comment_id
	/	$option - not used locally, needs to be passed
	**/
    function userdeletecomment($user_id, $comment_id, $option = null, $typeof = "pcomment") {
		if ($typeof == "pcomment") {
			$this->Pcomment->id = $comment_id;
			$this->Pcomment->read();
			$this->Pcomment->saveField("comment_visibility", "delbyadmin") ;
			$this->set('option', $option);
			$this->set('data', $this->Pcomment->findAll("Pcomment.user_id = $user_id", null, "Pcomment.timestamp DESC"));
			$this->redirect('/administration/viewuser/' . $user_id . '/' . $option);
		} elseif ($typeof == "gcomment") {
			$this->Gcomment->id = $comment_id;
			$this->Gcomment->read();
			$this->Gcomment->saveField("comment_visibility", "delbyadmin") ;
			$this->set('option', $option);
			$this->set('data', $this->Gcomment->findAll("Gcomment.user_id = $user_id", null, "Gcomment.timestamp DESC"));
			$this->redirect('/administration/viewuser/' . $user_id . '/' . $option);
		}
    }
    
	function delete_comment_ajax($user_id, $comment_id, $typeof = "pcomment") {
		if ($typeof == "pcomment") {
			$this->Pcomment->id = $comment_id;
			$pcomments = $this->Pcomment->read();
			$this->Pcomment->saveField("comment_visibility", "delbyadmin") ;
			$this->Pcomment->deleteCommentsFromMemcache($pcomments['Pcomment']['project_id']);
			exit;
		} elseif ($typeof == "gcomment") {
			$this->Gcomment->id = $comment_id;
			$gcomments = $this->Gcomment->read();
			$this->Gcomment->saveField("comment_visibility", "delbyadmin") ;
			$this->Gcomment->deleteCommentsFromMemcache($gcomments['Gcomment']['gallery_id']);
			exit;
		}
	}
	/**
	/	Deletes a comment under Section: Comments
	/	$comment_id
	/	$option - not used locally, needs to be passed
	**/
    function cdeletecomment($comment_id, $option = null) {
    	$this->Pcomment->id = $comment_id;
    	$this->Pcomment->read();
    	$this->Pcomment->saveField("comment_visibility", "delbyadmin");
    	$this->set('option', $option);
    	$this->redirect('/administration/'.'csort/' . $option);
    }
    
	/**
	/	Restores a comment under Section: Comments
	/	$comment_id
	/	$option - not used locally, needs to be passed
	**/
    function cundeletecomment($comment_id, $option = null) {
    	$this->Pcomment->id = $comment_id;
    	$this->Pcomment->read();
    	$this->Pcomment->saveField("comment_visibility", "visible");
		
    	$this->set('option', $option);
    	$this->redirect('/administration/'.'csort/' . $option);
    }
    
	/**
	/	Restores a comment under Section: Comments
	/	$project_id
	/	$comment_id
	/	$option - not used locally, needs to be passed
	**/
    function undeletecomment($project_id, $comment_id, $option = null) {
    	$this->Pcomment->id = $comment_id;
    	$this->Pcomment->read();
    	$this->Pcomment->saveField("comment_visibility", "visible");
		
    	$this->set('option', $option);
    	$this->set('data', $this->Pcomment->findAll("project_id = $project_id", null, "Pcomment.timestamp DESC"));
    	$this->set('project_id', $project_id);
    	$this->setFlash("Comment deleted", FLASH_NOTICE_KEY);
    	$this->render('admincomments_ajax', 'ajax');
    }
    
	/**
	/	Censors project where id = $project_id
	/	$project_id
	**/
    function censorproject($project_id) {
    	$this->Project->bindUser();
    	$this->Project->id = $project_id;
    	$project = $this->Project->read();
    	$username = $project['User']['username'];
    	$project_title = $project['Project']['name'];
    	$urlname = $username;
    	
    	$message = 'Your project <a href="/projects/'.$username.'/'.$project_id.'">'.$project_title.'</a> has
			been removed because multiple Scratch members considered it
			inappropriate for the Scratch community. Please read the <a
			href="/terms">Terms of Use</a> or contact us for more info. Thank you
			and Scratch on!';
			
    	$this->Project->censor($project_id, $urlname, $this->getLoggedInUrlname());
		$this->setFlash("Project censored", FLASH_NOTICE_KEY);
		$this->redirect('/administration/'.'projects');
    }
    		
	
	/***
	/	Users ---------------------------------------------------------------------------------------------------------
	***/
	
	/**
	/ Section: Users Index
	**/
    function users() {
    	$this->redirect('/administration/'.'usort/' . 'created');
    }
    
	/**
	/ Deletes a user
	/ $user_id
	**/
    function deleteuser($user_id) {
		$this->hide_user($user_id, "delbyadmin");
    	
		$this->redirect('/administration/search');
    }
    
	/**
	/ Deletes a user
	/ $user_id
	**/
    function lockuser($user_id) {
    	$this->User->id = $user_id;
		$user = $this->User->read();
		$this->User->saveField('status', 'locked');
		if($this->isAdmin())
		{
			$this->notify('account_lock', $user_id, array());
		}
		$this->redirect('/administration/viewuser/'. $user_id);
    }
	
	function unlockuser($user_id) {
		$this->User->id = $user_id;
		$user = $this->User->read();
		$this->User->saveField('status', 'normal');
		$this->redirect('/administration/viewuser/'. $user_id);
	}
	
	/**
	/ Notifies a user
	/ $user_id
	**/
    function notifyuser($user_id) {
    	//TODO
    }
    
	/**
	/ Shows the details of the user
	/ $user_id
	/ $option - display option see below
	**/
    function viewuser($user_id, $option = "comments") {
    	$this->User->id = $user_id;
    	$this->modelClass = "User";
		$user = $this->User->read();
		$this->Pcomment->bindProject();
		$user_id = $user['User']['id'];
		
		$this->Pagination->show = 30;
		
		if ($option == "comments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", Array(), $options);
			$data = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Project']['user_id'];
				$temp_user = $this->User->find("User.id = $temp_user_id");
				$temp_user_name = $temp_user['User']['username'];
				$temp_comment['Project']['username'] = $temp_user_name;
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "gcomments") {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Gcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", Array(), $options);
			$data = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Gallery']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
				
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "dpcomments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id AND Pcomment.comment_visibility != 'visible' AND Pcomment.comment_visibility != 'delbyparentcomment'", Array(), $options);
			$data = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility != 'visible' AND Pcomment.comment_visibility != 'delbyparentcomment'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Project']['user_id'];
				$temp_user = $this->User->find("User.id = $temp_user_id");
				$temp_user_name = $temp_user['User']['username'];
				$temp_comment['Project']['username'] = $temp_user_name;
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "dPparentcomments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id  AND Pcomment.comment_visibility = 'delbyparentcomment' ", Array(), $options);
			$data = $this->Pcomment->findAll("Pcomment.user_id = $user_id  AND Pcomment.comment_visibility = 'delbyparentcomment' ", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Project']['user_id'];
				$temp_user = $this->User->find("User.id = $temp_user_id");
				$temp_user_name = $temp_user['User']['username'];
				$temp_comment['Project']['username'] = $temp_user_name;
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "dgcomments") {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Gcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility != 'visible' AND Gcomment.comment_visibility != 'delbyparentcomment'", Array(), $options);
			$data = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility != 'visible' AND Gcomment.comment_visibility != 'delbyparentcomment'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Gallery']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
				
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "dGparentcomments") {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Gcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'delbyparentcomment'", Array(), $options);
			$data = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'delbyparentcomment'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Gallery']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
				
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "mpcomments") {
			$this->modelClass = "Pcomment";
			$data = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'");
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$comment_id = $pcomment['Pcomment']['id'];
				$temp_user_id = $pcomment['Project']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Project']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Project']['username'] = $temp_user_name;
				}
				
				$mp_count = $this->Mpcomment->findCount("Mpcomment.comment_id = $comment_id");
				
				if ($mp_count > 0) {
					$final_data[$counter] = $temp_comment;
					$counter++;
				}
			}
			$this->set('data', $final_data);
		}
		if ($option == "mgcomments") {
			$this->modelClass = "Gcomment";
			$data = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'");
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $gcomment) {
				$temp_comment = $gcomment;
				$comment_id = $gcomment['Gcomment']['id'];
				$temp_user_id = $gcomment['Gallery']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
				
				$mg_count = $this->Mgcomment->findCount("Mgcomment.comment_id = $comment_id");
				
				if ($mg_count > 0) {
					$final_data[$counter] = $temp_comment;
					$counter++;
				}
			}
			$this->set('data', $final_data);
		}
		if ($option == "projects") {
			$this->Project->bindUser();
			
			$this->modelClass = "Project";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id AND Project.proj_visibility = 'visible'", Array(), $options);
			$data = $this->Project->findAll("Project.user_id = $user_id AND Project.proj_visibility = 'visible'", null, $order, $limit, $page);
			$this->set('data', $data);
		}
		if ($option == "cprojects") {
			$this->Project->bindUser();
			
			$this->modelClass = "Project";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id AND Project.proj_visibility != 'visible'", Array(), $options);
			$data = $this->Project->findAll("Project.user_id = $user_id AND Project.proj_visibility != 'visible'", null, $order, $limit, $page);
			$this->set('data', $data);
		}
		if ($option == "notifications") {
			$this->modelClass = "Notification";
			$options = array( 'show'=>25  );
			$count = $this->Notification->countAll($user_id, true);
			$this->Pagination->ajaxAutoDetect = false;
			list($order, $limit, $page) = $this->Pagination->init(null, null, $options,
													$count);
			$notifications = $this->Notification->getNotifications($user_id, $page, $limit, true);
			$username = $user['User']['username'];
			$this->set('username', $username);
			$this->set('data', $notifications);
		}
		
		$user_status = $user['User']['status'];
		$ban_record = $this->BlockedUser->find("BlockedUser.user_id = $user_id");
		$temp_user = $user;
		$temp_user['User']['reason'] = "";
		if (empty($ban_record)) {
			$temp_user['User']['reason'] = "";
		} else {
			$temp_user['User']['reason'] = $ban_record['BlockedUser']['reason'];
		}
		
		//connected ip list
		$stats = $this->ViewStat->findAll("ViewStat.user_id = $user_id", "DISTINCT user_id, ipaddress");
		$ips_array = Array();
		$has_ips =false;
		foreach ($stats as $current_stat) {
			$temp_stat = $current_stat;
			array_push($ips_array,$temp_stat['ViewStat']['ipaddress']);
		}
		$ips_list = implode(',',$ips_array);
		if(!empty($ips_list)):
		$conditions = "BlockedIp.ip  in (".$ips_list.") ";
		$has_ips = $this->BlockedIp->hasAny($conditions);
		endif;
		$this->set('has_ips',$has_ips);
		$this->set('user', $temp_user);
		$this->set('option', $option);
    }
    
	/**
	* AJAX Helper for pagination
	**/
	
	function renderUser($user_id, $option) {
		$this->autoRender = false;
		$this->User->id = $user_id;
    	$this->modelClass = "User";
		$user = $this->User->read();
		$this->Pcomment->bindProject();
		
		$this->Pagination->show = 30;
		
		if ($option == "comments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", Array(), $options);
			$data = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Project']['user_id'];
				$temp_user = $this->User->find("User.id = $temp_user_id");
				$temp_user_name = $temp_user['User']['username'];
				$temp_comment['Project']['username'] = $temp_user_name;
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "gcomments") {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Gcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", Array(), $options);
			$data = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Gallery']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
				
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "dpcomments") {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Pcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id AND Pcomment.comment_visibility != 'visible'", Array(), $options);
			$data = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility != 'visible'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Project']['user_id'];
				$temp_user = $this->User->find("User.id = $temp_user_id");
				$temp_user_name = $temp_user['User']['username'];
				$temp_comment['Project']['username'] = $temp_user_name;
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "dgcomments") {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Gcomment", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility != 'visible'", Array(), $options);
			$data = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility != 'visible'", null, $order, $limit, $page);
			
			$final_data = Array();
			$counter = 0;
			foreach ($data as $pcomment) {
				$temp_comment = $pcomment;
				$temp_user_id = $pcomment['Gallery']['user_id'];
				if (!empty($temp_user_id)) {
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				} else {
					$temp_user_name = "";
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
				
				$final_data[$counter] = $temp_comment;
				$counter++;
			}
			$this->set('data', $final_data);
		}
		if ($option == "projects") {
			$this->Project->bindUser();
			
			$this->modelClass = "Project";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id AND Project.proj_visibility = 'visible'", Array(), $options);
			$data = $this->Project->findAll("Project.user_id = $user_id AND Project.proj_visibility = 'visible'", null, $order, $limit, $page);
			$this->set('data', $data);
		}
		if ($option == "cprojects") {
			$this->Project->bindUser();
			
			$this->modelClass = "Project";
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id AND Project.proj_visibility != 'visible'", Array(), $options);
			$data = $this->Project->findAll("Project.user_id = $user_id AND Project.proj_visibility != 'visible'", null, $order, $limit, $page);
			$this->set('data', $data);
		}
		if ($option == "notifications") {
			$this->modelClass = "Notification";
			$options = Array("sortBy"=>"id", "sortByClass" => "Notification", 
						"direction"=> "DESC", "url"=>"/administration/renderUser/$user_id/" . $option);
			list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
			$data = $this->Notification->findAll("user_id = $user_id", null, $order, $limit, $page);
			
			$this->set('data', $data);		
		}
		
		$user_status = $user['User']['status'];
		$this->set('user', $user);
		$this->set('user_id', $user['User']['id']);
		$this->set('option', $option);
		$this->render('renderuser_ajax', 'ajax');
	}
	/**
	/ Sorts users
	/ $user_id
	/ $option - display option see below
	**/
    function usort($options = null, $results = null) {
    	$this->autoRender = false;
    	$this->modelClass = "User";
    	list($order,$limit,$page) = $this->Pagination->init();
    	$order = "User.created DESC";
    	$this->set('option', $options);
    	
    	if ($options == "created") {
    		$order = "User.created DESC";
    	}
    	if ($options == "name") {
    		$order = "User.username ASC";
    	}
    	if ($options == "country") {
    		$order = "User.country ASC";
    	}
    	if ($options == "birth") {
    		$order = "User.byear DESC";
    	}
    
	//Checks filter
    if (!empty($this->data['Filter'])) {
    		$paginate = array('limit' => 10, 'page' => 1); 
    		$key = $this->data['Filter']['key'];
    		$haystack = $this->User->findAll("User.username = '$key'", NULL, $order,$limit,$page);
    		$this->set('data', $haystack);
    		$this->render('users');
    	} else {
    		$this->set('data', $this->User->findAll(NULL, NULL, $order, $limit, $page));
    		$this->render('users');
    	}
    }
    
	/***
	/	Comments ------------------------------------------------------------------------------------------------------
	***/
	
	/**
	/ Comments Index
	**/
    function comments() {
    	$this->redirect('/administration/'.'csort/' . 'created');
    }
    
	/**
	/ Comments Index
	**/
    function csort($options) {
    	$this->autoRender = false;
    	$this->modelClass = "Pcomment";
    	$this->Pcomment->bindUser();
    	$this->Pcomment->bindProject();
		$this->Project->bindUser();
    	$this->set('option', $options);
    	list($order,$limit,$page) = $this->Pagination->init();
    	
    	if ($options == "created") {
    		$order = "Pcomment.timestamp DESC";
    	}
    	if ($options == "creator") {
    		$order = "User.username ASC";
    	}
    	if ($options == "project") {
    		$order = "Project.name ASC";
    	}
		$data = $this->Pcomment->findAll(NULL, NULL, $order, $limit, $page);
		$final_comments = array();
		$counter = 0;
		foreach ($data as $comment) {
			$project_id = $comment['Pcomment']['project_id'];
			$project = $this->Project->find("Project.id = $project_id");
			$temp_comment = $comment;
			$temp_comment['Pcomment']['urlname'] = $project['User']['urlname'];
			$final_comments[$counter] = $temp_comment;
			$counter++;
		}
		
    	$this->set('data', $final_comments);
    	$this->render('comments');
    }
    
	/***
	/	Tags ---------------------------------------------------------------------------------------------------------
	***/
	
	/**
	/ Tags Index
	**/
    function tags() {
    	$this->modelClass = "Tag";
    	list($order,$limit,$page) = $this->Pagination->init();
    	$order = "Tag.name ASC";
    	$this->set('data', $this->Tag->findAll(NULL, NULL, $order, $limit, $page));
    }
    
	/**
	/ Tag sorting/ordering
	**/
    function tsort($options) {
    	$this->modelClass = "Tag";
    }
    
	
	/***
	/	Galleries ----------------------------------------------------------------------------------------------------
	
	***/
	
	/**
	/ Galleries Index
	**/
    function galleries() {
    	$this->modelClass = "Gallery";
    	list($order,$limit,$page) = $this->Pagination->init();
    	$this->Gallery->bindUser();
    	$order = "Gallery.name ASC";
    	$this->set('data', $this->Gallery->findAll(NULL, NULL, $order, $limit, $page));
    }
    
	/**
	/ Galleries Index
	**/
    function gsort($options) {
    	$this->autoRender = false;
    	$this->modelClass = "Gallery";
    	$this->Gallery->bindUser();
    	list($order,$limit,$page) = $this->Pagination->init();
    	if ($options == "created") {
    		$order = "Gallery.timestamp DESC";
    	}
    	if ($options == "creator") {
    		$order = "User.username ASC";
    	}
    	if ($options == "name") {
    		$order = "Gallery.name ASC";
    	}
    	$this->set('data', $this->Gallery->findAll(NULL, NULL, $order, $limit, $page));
    	$this->render('galleries');
    }
	
	/**
	/ Feature a Gallery
	**/
	function featureGallery($gallery_id) {
		$this->autoRender = false;
		$featured_galleries = $this->FeaturedGallery->findall();
		$duplicate = false;
		foreach($featured_galleries as $current_gallery) {
			if ($current_gallery['FeaturedGallery']['gallery_id'] == $gallery_id) {
				$duplicate = true;
			}
		}
		if ($duplicate == false) {
			$info = Array('FeaturedGallery' => Array('id' => null, 'gallery_id' => $gallery_id));
			$this->FeaturedGallery->save($info);
			$this->render('void_ajax', 'ajax');
			exit();
		} else {
			$this->setFlash("Project Clubbed", FLASH_NOTICE_KEY);
			$this->render('void_ajax', 'ajax');
			exit();
		}
	}
	
	/**
	/ Club a Gallery
	**/
	function clubGallery($gallery_id) {
		$this->autoRender = false;
		$clubbed_galleries = $this->ClubbedGallery->findall();
		$duplicate = false;
		foreach($clubbed_galleries as $current_gallery) {
			if ($current_gallery['ClubbedGallery']['gallery_id'] == $gallery_id) {
				$duplicate = true;
			}
		}
		if ($duplicate == false) {
			$info = Array('ClubbedGallery' => Array('id' => null, 'gallery_id' => $gallery_id));
			$this->ClubbedGallery->save($info);
			$this->render('void_ajax', 'ajax');
			exit();
		} else {
			$this->setFlash("Project Clubbed", FLASH_NOTICE_KEY);
			$this->render('void_ajax', 'ajax');
			exit();
		}
	}
	
	 /**
     * Admin comment action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function add_admin_note($project_id = null, $option) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$project_id;
		$this->Apcomment->bindUser();
        $project = $this->Project->read();

        if (empty($project))
            exit();
	
        if (!empty($this->params['form'])) {
			$comment = htmlspecialchars($this->params['form']['apcomment_textarea']);
				
			$new_apcomment = array('Apcomment'=>array('project_id'=>$project_id, 'content'=>$comment, 'created' => date("Y-m-d G:i:s") ));
			$this->Apcomment->save($new_apcomment);
        }

		$counter = 0;
		$final_comments = Array();
		$comments = $this->Apcomment->findAll("project_id = $project_id", null, "Apcomment.timestamp DESC");
		foreach ($comments as $current_comment) {
			$temp_comment = $current_comment;
			$final_comments[$counter] = $temp_comment;
			$counter++;
		}
		
		$isLogged = $this->isLoggedIn();
		$this->set('option', $option);
		$this->set('project_id', $project_id);
		$this->set('data',$final_comments);
		$this->render('admincomments_ajax', 'ajax');
		return;
	}
	
	/**
	* Adds an admin comment for user_id = $user_id
	**/
	function add_admin_comment($user_id = null) {
		$this->autoRender=false;
        $this->User->bindProject();
        $this->User->id=$user_id;
		$this->AdminComment->bindUser();
        $user = $this->User->read();
		$isLogged = $this->isLoggedIn();

        if (empty($user))
            exit();
	
        if (!empty($this->params['form'])) {
			$comment = htmlspecialchars($this->params['form']['apcomment_textarea']);
			
			$comment_record = $this->AdminComment->findCount("user_id = $user_id");
			if ($comment_record > 0) {
				$full_comment = $this->AdminComment->find("user_id = $user_id");
				$this->AdminComment->id = $full_comment['AdminComment']['id'];
				$this->AdminComment->saveField("content", $comment);
				$new_acomment = $comment;
			} else {
				$new_acomment = array('AdminComment'=>array('id' => null, 'user_id' => $user_id, 'content'=>$comment, 'created' => date("Y-m-d G:i:s") ));
				$this->AdminComment->save($new_acomment);
			}
        }
		
		
		$this->set('user_id', $user['User']['id']);
		$this->set('comment',$comment);
		$this->render('adminusercomment_ajax', 'ajax');
		return;
	}
	
	/**
	* Renders search
	* $option = type of search requested
	**/
	function search($option = null) {
		$this->autoRender = false;
		
		if ($option == null) { 
			$current_option = "users";
		} else {
			$current_option = $option;
		}
		
		$this->set('option', $current_option);
		$this->render('search');
	}
	
	/**
	* Executes search
	* $table - database table to be used
	**/
	function execute_search($table) {
		$this->autoRender = false;
		$isBanned =false;
		$search_table = $table;
		if (!empty($this->params['form'])) {
			$search_term = htmlspecialchars($this->params['form']['admin_search_textarea']);
			$search_column = $this->params['form']['column'];
        } else {
			$search_term  = "";
			$search_column = "";
		}
		
		if ($search_table == 'users') {
			 // get content for "more project" browser
			$this->Pagination->show = 10;
			$this->modelClass = "User";
			
			if ($search_column == 'Name') {
				$options = Array("sortBy"=>"username", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				$search_term = "%" . $search_term . "%";
				list($order,$limit,$page) = $this->Pagination->init("username LIKE '$search_term'", Array(), $options);
				$results = $this->User->findAll("User.username LIKE '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Email') {
				$options = Array("sortBy"=>"email", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("email = '$search_term'", Array(), $options);
				$results = $this->User->findAll("email = '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Country') {
				$options = Array("sortBy"=>"country", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("country = '$search_term'", Array(), $options);
				$results = $this->User->findAll("country = '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Birth Year') {
				$options = Array("sortBy"=>"byear", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("byear = $search_term", Array(), $options);
				$results = $this->User->findAll("byear = $search_term", null, $order, $limit, $page);
			}
			
			if ($search_column == 'ipaddress') {
				$search_ip =ip2long($search_term);
				
				$options = Array("sortBy"=>"created", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("ipaddress = $search_ip", Array(), $options);
				$users = $this->User->findAll("ipaddress = $search_ip", null, $order, $limit, $page);
				foreach($users as $result){
					$view_details =  $this->set_view_date_time($search_term, $result['User']['id']);
					 $result['User']['last_access_date_time'] = $view_details;
					
					$results[] =$result;
					
				}
			}
		} elseif ($search_table == 'projects') {
			$this->Pagination->show = 10;
			$this->modelClass = "Project";
			
			if ($search_column == 'Name') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				$search_term = "%" . $search_term . "%";
				list($order,$limit,$page) = $this->Pagination->init("Project.name LIKE'$search_term'", Array(), $options);
				$results = $this->Project->findAll("Project.name LIKE '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Creator') {
				$user_name = $search_term;
				$user_record = $this->User->findAll("username = '$user_name'");
				if (!empty($user_record)) {
					$user_id = $user_record[0]['User']['id'];
				} else {
					$user_id = -1;
				}
				$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
				$results = $this->Project->findAll("user_id = $user_id", null, $order, $limit, $page);
			}
			if ($search_column == 'Status') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("status = '$search_term'", Array(), $options);
				$results = $this->Project->findAll("status = '$search_term'", null, $order, $limit, $page);
			}
		} elseif ($search_table == 'galleries') {
			$this->Pagination->show = 10;
			$this->modelClass = "Gallery";
			$this->Gallery->bindUser();
			
			if ($search_column == 'Name') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				$search_term = "%" . $search_term . "%";
				list($order,$limit,$page) = $this->Pagination->init("Gallery.name LIKE '$search_term'", Array(), $options);
				$results = $this->Gallery->findAll("Gallery.name LIKE '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Creator') {
				$user_name = $search_term;
				$user_record = $this->User->findAll("username = '$user_name'");
				if (!empty($user_record)) {
					$user_id = $user_record[0]['User']['id'];
				} else {
					$user_id = -1;
				}
				$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
				$results = $this->Gallery->findAll("user_id = $user_id");
			}
			if ($search_column == 'Status') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("status = '$search_term'", Array(), $options);
				$results = $this->Gallery->findAll("status = '$search_term'", null, $order, $limit, $page);
			}
		}
		
		$this->set('results', $results);
		$this->set('search_term', $search_term);
		$this->set('search_column', $search_column);
		$this->set('search_table', $search_table);
		if($search_column == 'ipaddress') {
            $search_ip =ip2long($search_term);
			$is_allow_account_creation = true;
			$this->BlockedIp->unbindModel(
                array('belongsTo' => array('User'))
            );
             $locked_id = $this->checkLockedUser($search_term); 
			 if($locked_id){
			 	$is_allow_account_creation = false;
				}
			$this->set('is_allow_account_creation', $is_allow_account_creation);
			$this->set('banned',$this->BlockedIp->find("BlockedIp.ip=$search_ip"));
			$this->set('isWhitelisted',$this->WhitelistedIpAddress->find("WhitelistedIpAddress.ipaddress =$search_ip"));
            $this->set('orig_ip',$search_ip);
            $this->set('search_term',$search_term);
            $this->render('admin_search_ip', 'ajax');
		}
		else {
            $this->render('admin_search', 'ajax');
        }
	}
	
	function set_view_date_time($ip, $user_id){
	 	return $view_details = $this->ViewStat->field('timestamp', "ipaddress = INET_ATON('$ip') && user_id = $user_id", 'timestamp DESC');
	 	
	}
	
	//used for unblocked ip and override prevention of account creation from ip by creating ip as whitelist
	function unblock_ip($ip = null, $search_ip =null){
	if($search_ip){
		$this->add_whitelisted_ip($search_ip);
	}
	else{
			$data = $this->BlockedIp->find("BlockedIp.ip=$ip");
			$ip_id = $data['BlockedIp']['id'];
			$this->BlockedIp->del($ip_id);
			$this->redirect('/administration/search');
		}
	
	}//function
	
	function add_whitelisted_ip($ip){
		$ip_whitelisted = $this->WhitelistedIpAddress->hasAny("WhitelistedIpAddress.ipaddress = INET_ATON('$ip')");
		if(!$ip_whitelisted){
		//make ip as whitelisted
                $sql = "INSERT INTO `whitelisted_ip_addresses` (`id`,`ipaddress`) VALUES"
                        ." (NULL, INET_ATON('$ip'))";
                $this->WhitelistedIpAddress->query($sql);
				
			}
			$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	function remove_whitelisted_ip($ip){
			$data = $this->WhitelistedIpAddress->find("WhitelistedIpAddress.ipaddress = $ip");
			$ip_id = $data['WhitelistedIpAddress']['id'];
			$this->WhitelistedIpAddress->del($ip_id);
			$this->redirect('/administration/search');
	}
 
	/** 
	* Ajax helper for pagination of search results
	* $searcH_table = table to be searched
	* $search_column = column to be searched
	* $search_term = search term to be returned
	**/
	function render_results($search_table = null, $search_column = null, $search_term = null) {
		$this->autoRender = false;
		
		if ($search_table == 'users') {
			$this->Pagination->show = 10;
			$this->modelClass = "User";
			
			if ($search_column == 'Name') {
				$options = Array("sortBy"=>"username", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				$search_term = "%" . $search_term . "%";
				list($order,$limit,$page) = $this->Pagination->init("User.username LIKE '$search_term'", Array(), $options);
				$results = $this->User->findAll("User.username LIKE '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Email') {
				$options = Array("sortBy"=>"email", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("email = '$search_term'", Array(), $options);
				$results = $this->User->findAll("email = '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Country') {
				$options = Array("sortBy"=>"country", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("country = '$search_term'", Array(), $options);
				$results = $this->User->findAll("country = '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Birth Year') {
				$options = Array("sortBy"=>"byear", "sortByClass" => "User", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("byear = $search_term", Array(), $options);
				$results = $this->User->findAll("byear = $search_term", null, $order, $limit, $page);
			}
		} elseif ($search_table == 'projects') {
			$this->Pagination->show = 10;
			$this->modelClass = "Project";
			
			if ($search_column == 'Name') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				$search_term = "%" . $search_term . "%";
				list($order,$limit,$page) = $this->Pagination->init("Project.name LIKE '$search_term'", Array(), $options);
				$results = $this->Project->findAll("Project.name LIKE '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Creator') {
				$user_name = $search_term;
				$user_record = $this->User->findAll("username = '$user_name'");
				if (!empty($user_record)) {
					$user_id = $user_record[0]['User']['id'];
				} else {
					$user_id = -1;
				}
				$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
				$results = $this->Project->findAll("user_id = $user_id", null, $order, $limit, $page);
			}
			if ($search_column == 'Status') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("status = '$search_term'", Array(), $options);
				$results = $this->Project->findAll("status = '$search_term'", null, $order, $limit, $page);
			}
		} elseif ($search_table == 'galleries') {
			$this->Pagination->show = 10;
			$this->modelClass = "Gallery";
			$this->Gallery->bindUser();
			
			if ($search_column == 'Name') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				$search_term = "%" . $search_term . "%";
				list($order,$limit,$page) = $this->Pagination->init("Gallery.name LIKE '$search_term'", Array(), $options);
				$results = $this->Gallery->findAll("Gallery.name LIKE '$search_term'", null, $order, $limit, $page);
			}
			if ($search_column == 'Creator') {
				$user_name = $search_term;
				$user_record = $this->User->findAll("username = '$user_name'");
				if (!empty($user_record)) {
					$user_id = $user_record[0]['User']['id'];
				} else {
					$user_id = -1;
				}
				$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
				$results = $this->Gallery->findAll("user_id = $user_id");
			}
			if ($search_column == 'Status') {
				$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
							"direction"=> "DESC", "url" => "/administration/render_results/" . $search_table . "/" . $search_column . "/" . $search_term);
				list($order,$limit,$page) = $this->Pagination->init("status = '$search_term'", Array(), $options);
				$results = $this->Gallery->findAll("status = '$search_term'", null, $order, $limit, $page);
			}
		}
		
		$this->set('results', $results);
		$this->set('search_term', $search_term);
		$this->set('search_column', $search_column);
		$this->set('search_table', $search_table);
		$this->render('admin_search', 'ajax');
	}
	
	/**
	* Announcement tool
	**/
	function set_announcements() {
		$this->autoRender = false;
		$announcements = $this->Announcement->findAll();
		$a_1 = $announcements[0]['Announcement']['content'];
		$a_2 = $announcements[1]['Announcement']['content'];
		$a_3 = $announcements[2]['Announcement']['content'];
		$isAnnouncementOn = $announcements[0]['Announcement']['isOn'];
		
		$this->set('a_1', $a_1);
		$this->set('a_2', $a_2);
		$this->set('a_3', $a_3);
		$this->set('isAnnouncementOn', $isAnnouncementOn);
		$this->render('set_announcements');
	}
	
	/**
	* Updates Announcements
	**/
	function update_announcements() {
		$this->autoRender = false;
		if (!empty($this->params['form'])) {
			$a_1 = $this->params['form']['a_1_textarea'];
			$a_2 = $this->params['form']['a_2_textarea'];
			$a_3 = $this->params['form']['a_3_textarea'];
        } else {
			$a_1  = "";
			$a_2 = "";
			$a_3 = "";
		}
		
		$user_id = $this->getLoggedInUserID();
		$this->Announcement->id = 1;
		$this->Announcement->read();
		$this->Announcement->saveField('content', $a_1);
		$this->Announcement->saveField('user_id', $user_id);
		$this->Announcement->id = 2;
		$this->Announcement->read();
		$this->Announcement->saveField('content', $a_2);
		$this->Announcement->saveField('user_id', $user_id);
		$this->Announcement->id = 3;
		$this->Announcement->read();
		$this->Announcement->saveField('content', $a_3);
		$this->Announcement->saveField('user_id', $user_id);
		
		$this->set('a_1', $a_1);
		$this->set('a_2', $a_2);
		$this->set('a_3', $a_3);
		$this->render('update_announcements_ajax', 'ajax');
		return;
	}
	
	/**
	* Toggles whether projects are hidden or shown
	**/
	function set_announcement_visibility($visibility = null) {
		$this->autoRender = false;
		
		if ($visibility == 0) 
			$isAnnouncementOn = false;
		else {
			$isAnnouncementOn = true;
		}
		
		$user_id = $this->getLoggedInUserID();
		$this->Announcement->id = 1;
		$this->Announcement->read();
		$this->Announcement->saveField('isOn', $visibility);
		$this->Announcement->id = 2;
		$this->Announcement->read();
		$this->Announcement->saveField('isOn', $visibility);
		$this->Announcement->id = 3;
		$this->Announcement->read();
		$this->Announcement->saveField('isOn', $visibility);
		
		$this->set('isAnnouncementOn', $isAnnouncementOn);
		$this->render('set_announcement_visibility_ajax', 'ajax');
	}
	
	/**
	* RSS Feeds
	**/
	function rss_feeds() {
		$this->autoRender = false;
		$this->render('rss_feeds');
	}
	
	/**
	* IP Ban
	**/
	function ban_ip($user_id = "", $overload = "") {
		$this->autoRender = false;
		$this->Pagination->show = 20;
		$this->checkPermission('block_IP');
		$this->modelClass = "BlockedIp";
		$options = Array("sortBy"=>"id", "sortByClass" => "BlockedIp", 
							"direction"=> "DESC", "url" => "/administration/render_ips/");
							
		list($order,$limit,$page) = $this->Pagination->init(null, Array(), $options);
		$results = $this->BlockedIp->findAll(null, null, $order, $limit, $page);
		
		$final_ips = Array();
		foreach ($results as $ip) {
			$temp_ip = $ip;
			$old_ip = $ip['BlockedIp']['ip'];
			$new_ip = long2ip($ip['BlockedIp']['ip']);
			$temp_ip['BlockedIp']['converted_ip'] = $new_ip;
			array_push($final_ips, $temp_ip);
		}
		
		if ($user_id == "" && $overload == "") {
			$user_name = "";
			$actual_ip = "";
		} else {
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			$actual_ip = $overload;
		}
		//list user linked to this ip 
		$final_users = Array();
		if($overload){
			$counter = 0;
            $this->ViewStat->recursion = -1;
			$stats = $this->ViewStat->findAll("ViewStat.ipaddress = INET_ATON('$overload') AND ViewStat.user_id != 0", "DISTINCT user_id, ipaddress");
			foreach ($stats as $current_stat) {
				$temp_stat = $current_stat;
				$current_user_id = $temp_stat['ViewStat']['user_id'];
				$current_user = $this->User->find("User.id = $current_user_id");
				$temp_stat['User'] = $current_user;
				$final_users[$counter] = $temp_stat;
				$counter++;
			}
		}
		$this->set('isError', false);
		$this->set('errors', Array());
		$this->set('default_name', $user_name);
		$this->set('default_ip', $actual_ip);
		$this->set('users', $final_users);
		$this->set('data', $final_ips);
		$this->render('ban_ip');
	}
	
	/**
	* Handles viewing of all users who use a particular ip address
	**/
	function expand_ip($ip) {
		$this->autoRender = false;
		
		$final_users = Array();
		$counter = 0;
		$stats = $this->ViewStat->findAll("ViewStat.ipaddress = INET_ATON('$ip') AND ViewStat.user_id != 0", "DISTINCT user_id, ipaddress");
		foreach ($stats as $current_stat) {
			$temp_stat = $current_stat;
			$current_user_id = $temp_stat['ViewStat']['user_id'];
			$current_user = $this->User->find("User.id = $current_user_id");
			$temp_stat['User'] = $current_user;
			$final_users[$counter] = $temp_stat;
			$counter++;
		}

        $this->set('ip', $ip);
		$this->set('data', $final_users);
		$this->render('expand_ip');
	}
	
	/**
	* IP Ban Pagination Helper
	**/
	function render_ips() {
		$this->autoRender = false;
		$this->checkPermission('block_IP');
		$this->Pagination->show = 20;
		
		$this->modelClass = "BlockedIp";
		$options = Array("sortBy"=>"id", "sortByClass" => "BlockedIp", 
							"direction"=> "DESC", "url" => "/administration/render_ips/");
							
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
		$results = $this->BlockedIp->findAll("", null, $order, $limit, $page);
		
		$final_ips = Array();
		foreach ($results as $ip) {
			$temp_ip = $ip;
			$old_ip = $ip['BlockedIp']['ip'];
			$new_ip = long2ip($ip['BlockedIp']['ip']);
			echo $new_ip;
			$temp_ip['BlockedIp']['converted_ip'] = $new_ip;
			array_push($final_ips, $temp_ip);
		}
		$this->set('data', $final_ips);
		$this->render('render_ips_ajax', 'ajax');
	}
	
	/**
	* Bans Target IP
	**/
	function add_ban_ip() {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$errors=array();
		if (!empty($this->params['form'])) {
			$new_ip = $this->params['form']['admin_banip_ip'];
			$new_reason = htmlspecialchars($this->params['form']['admin_banip_reason']);
			if(empty($new_ip))
			array_push($errors, "Enter Ip to ban.");
			if(empty($new_reason))
			array_push($errors, "Enter Reason.");
			if($new_ip && $new_reason)
			{
			$ban_ip = ip2long($new_ip);
			$ip_record = $this->BlockedIp->find("BlockedIp.ip = $ban_ip");
				if (empty($ip_record)) 
				{
					$info = Array('BlockedIp' => Array('id' => null, 'user_id' => $user_id, 'ip' => $ban_ip, 'reason' => $new_reason));				$this->BlockedIp->save($info);
				}
				else
				{
				array_push($errors, "This Ip has already been banned.");
				}
			}
        } 
		else 
		{
			
		}
		
		$this->Pagination->show = 20;
		
		$this->modelClass = "BlockedIp";
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "BlockedIp", 
							"direction"=> "DESC", "url" => "/administration/render_ips/");
							
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
		$results = $this->BlockedIp->findAll("", null, $order, $limit, $page);
		
		$final_ips = Array();
		foreach ($results as $ip) {
			$temp_ip = $ip;
			$old_ip = $ip['BlockedIp']['ip'];
			$new_ip = long2ip($ip['BlockedIp']['ip']);
			$temp_ip['BlockedIp']['converted_ip'] = $new_ip;
			array_push($final_ips, $temp_ip);
		}
		
		if (empty($errors)) {
			$isError = false;
		} else {
			$isError = true;
		}
		$this->set('isError', $isError);
		$this->set('errors', $errors);
		$this->set('default_ip'," ");
		$this->set('default_name'," ");
		$this->set('data', $final_ips);
		$this->render('render_ips_ajax', 'ajax');
	}
	
	/**
	* Remove banned ip
	**/
	function remove_ban_ip($ip_id) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$this->Pagination->show = 20;
		$this->BlockedIp->del($ip_id);
		
		$this->modelClass = "BlockedIp";
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "BlockedIp", 
							"direction"=> "DESC", "url" => "/administration/render_ips/");
							
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
		$results = $this->BlockedIp->findAll("", null, $order, $limit, $page);
		
		$final_ips = Array();
		foreach ($results as $ip) {
			$temp_ip = $ip;
			$old_ip = $ip['BlockedIp']['ip'];
			$new_ip = long2ip($ip['BlockedIp']['ip']);
			$temp_ip['BlockedIp']['converted_ip'] = $new_ip;
			array_push($final_ips, $temp_ip);
		}
		$this->set('data', $final_ips);
		$this->set('isError', false);
		$this->render('render_ips_ajax', 'ajax');
	}
	
	/**
	* Handles user/ip banning
	**/
	function ban_user($user_id = "", $overload = "") {	
		$this->autoRender = false;
		$this->User->id = $user_id;
		$this->checkPermission('block_account');
		$banned_users = $this->set_banned_users();
		
		if ($user_id == "" && $overload == "") {
			$user_name = "";
			$reason = "";
		} else {
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			$reason = $overload;
		}
		
		
		$this->set('isError', false);
		$this->set('errors', Array());
		$this->set('default_name', $user_name);
		$this->set('default_reason', $reason);
		$this->set('data', $banned_users);
		$this->render('ban_user');
	}
	
	/**
	* Adds an user to the banned users list
    * used in administration/ban_user form
	**/
	function add_banned_user() {
		$this->autoRender = false;
		$admin_id = $this->getLoggedInUserID();
		$errors = Array();
		
		if (!empty($this->params['form']['admin_banuser_name'])) {
			$username = $this->params['form']['admin_banuser_name'];
			$reason = htmlspecialchars($this->params['form']['admin_banuser_reason']);
			
			$user_record = $this->User->find("User.username = '$username'");
			
			if (empty($user_record)) {
				array_push($errors, "That user does not exist.");
			} else {
				$current_user_id = $user_record['User']['id'];
				$blocked_record = $this->BlockedUser->find("BlockedUser.user_id = $current_user_id");
				//not already blocked
                if (empty($blocked_record)) {
					$this->User->id = $current_user_id;
					if($this->User->saveField("status", 'locked')) {
                        $info = Array('BlockedUser' => Array('id' => null, 'user_id' => $current_user_id, 'admin_id' => $admin_id, 'reason' => $reason));
                        $this->BlockedUser->save($info);
                    }
                    else {
                        array_push($errors, "Unknown exception, please contact andresmh@media.mit.edu");
                    }
				}
                else {
					array_push($errors, "That user has already been banned.");
				}
			}
        } else {
			array_push($errors, "Please enter a valid username.");
		}
	
		if (empty($errors)) {
			$isError = false;
		} else {
			$isError = true;
		}
		
		if ($isError) {
			$banned_users = $this->set_banned_users();
		} else {
			$banned_users = $this->set_banned_users("", $current_user_id, $reason);
		}
		

		$this->set('isError', $isError);
		$this->set('errors', $errors);
		$this->set('default_name', "");
		$this->set('default_reason', "");
		$this->set('data', $banned_users);
		$this->render('render_banned_users_list_ajax', 'ajax');
	}
	
	/**
	* Pagination helper for rendering banned users
	**/
	function render_banned_users() {
		$this->autoRender = false;
		
		$banned_users = $this->set_banned_users();
		
		$this->set('data', $banned_users);
		$this->set('default_name', "");
		$this->set('default_reason', "");
		$this->render('render_banned_users_ajax', 'ajax');
	}
	
	/**
	* Remove banned ip
	**/
	function remove_banned_user($banned_id) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		
		$ban_record = $this->BlockedUser->find("BlockedUser.user_id = $banned_id");
		$ban_id = $ban_record['BlockedUser']['id'];
		$this->User->id = $banned_id;
		$banned_user = $this->User->read();
		$this->User->saveField("status", 'normal');
		$this->BlockedUser->del($ban_id);
		
		$banned_users = $this->set_banned_users();
		
		$this->set('data', $banned_users);
		$this->set('default_name', "");
		$this->set('default_reason', "");
		$this->render('render_banned_users_ajax', 'ajax');
	}
	
	/**
	* Helper for setting additional data needed for banned users
	**/
	function set_banned_users($banned_users = "", $overload_id = "", $overload_reason = "") {
		$this->modelClass = "BlockedUser";
		$this->Pagination->show = 20;
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "BlockedUser", 
							"direction"=> "DESC", "url" => "/administration/render_banned_users/");
							
		list($order,$limit,$page) = $this->Pagination->init("User.id > 0", Array(), $options);
		$banned_users = $this->BlockedUser->findAll("User.id > 0", null, $order, $limit, $page);
		
		$return_array = Array();
		foreach ($banned_users as $banned_user) {
			$temp_user = $banned_user;
			$current_user_id = $banned_user['User']['id'];
			$ban_record = $this->BlockedUser->find("BlockedUser.user_id = $current_user_id");
			$status = $banned_user['User']['status'];
			
			if ($overload_id == $current_user_id) {
				$temp_user['User']['reason'] = $overload_reason;
			} else {
				$temp_user['User']['reason'] = $ban_record['BlockedUser']['reason'];
			}
			if ($status == 'locked') {
				if (empty($ban_record)) {
					$default_reason = "You have violated our Terms of Service. ";
					if ($overload_id == "" && $overload_reason == "") {
						$temp_user['User']['reason'] = $default_reason;
					} else {
						if ($overload_id == $current_user_id) {
							$temp_user['User']['reason'] = $overload_reason;
						}
					}
				} else {
					$temp_user['User']['reason'] = $ban_record['BlockedUser']['reason'];
				}
			}
			
			array_push($return_array, $temp_user);
		}
		return $return_array;
	}
	
	/**
	* Handles username banning from front page
	**/
	function ban_user_frontpage($user_id = "", $overload = "") {	
		$this->autoRender = false;
		$this->User->id = $user_id;
		
		$banned_users = $this->set_banned_users_frontpage();
		
		if ($user_id == "" && $overload == "") {
			$user_name = "";
			$reason = "";
		} else {
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			$reason = $overload;
		}
		
		
		$this->set('isError', false);
		$this->set('errors', Array());
		$this->set('default_name', $user_name);
		$this->set('default_reason', $reason);
		$this->set('data', $banned_users);
		$this->render('ban_user_frontpage');
	}
	
	/**
	* Adds an user to the banned users list
    * used in administration/ban_user form
	**/
	function add_banned_user_frontpage() {
		$this->autoRender = false;
		$admin_id = $this->getLoggedInUserID();
		$errors = Array();
		
		if (!empty($this->params['form']['admin_banuser_name'])) {
			$username = $this->params['form']['admin_banuser_name'];
			$reason = htmlspecialchars($this->params['form']['admin_banuser_reason']);
			
			$user_record = $this->User->find("User.username = '$username'");
			
			if (empty($user_record)) {
				array_push($errors, "That user does not exist.");
			} else {
				$current_user_id = $user_record['User']['id'];
				$blocked_record = $this->BlockedUserFrontpage->find("BlockedUserFrontpage.user_id = $current_user_id");
				//not already blocked
                if (empty($blocked_record)) {
					$this->User->id = $current_user_id;
					
                        $info = Array('BlockedUserFrontpage' => Array('id' => null, 'user_id' => $current_user_id, 'admin_id' => $admin_id, 'reason' => $reason));
                        $this->BlockedUserFrontpage->save($info);
                        $this->BlockedUserFrontpage->mc_connect();
                        $this->BlockedUserFrontpage->mc_delete('home_projects');
                        $this->BlockedUserFrontpage->mc_close();
                    
				}
                else {
					array_push($errors, "That user has already been banned.");
				}
			}
        } else {
			array_push($errors, "Please enter a valid username.");
		}
	
		if (empty($errors)) {
			$isError = false;
		} else {
			$isError = true;
		}
		
		if ($isError) {
			$banned_users = $this->set_banned_users_frontpage();
		} else {
			$banned_users = $this->set_banned_users_frontpage("", $current_user_id, $reason);
		}
		

		$this->set('isError', $isError);
		$this->set('errors', $errors);
		$this->set('default_name', "");
		$this->set('default_reason', "");
		$this->set('data', $banned_users);
		$this->render('render_banned_users_frontpage_list_ajax', 'ajax');
	}
	
	/**
	* Pagination helper for rendering banned users
	**/
	function render_banned_users_frontpage() {
		$this->autoRender = false;
		
		$banned_users = $this->set_banned_users_frontpage();
		
		$this->set('data', $banned_users);
		$this->set('default_name', "");
		$this->set('default_reason', "");
		$this->render('render_banned_users_frontpage_ajax', 'ajax');
	}
	
	
	/**
	* Helper for setting additional data needed for banned users
	**/
	function set_banned_users_frontpage($banned_users = "", $overload_id = "", $overload_reason = "") {
		$this->modelClass = "BlockedUserFrontpage";
		$this->Pagination->show = 20;
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "BlockedUserFrontpage", 
							"direction"=> "DESC", "url" => "/administration/render_banned_users_frontpage/");
							
		list($order,$limit,$page) = $this->Pagination->init("User.id > 0", Array(), $options);
		$banned_users = $this->BlockedUserFrontpage->findAll(null, null, $order, $limit, $page);
		
		$return_array = Array();
		foreach ($banned_users as $banned_user) {
			$temp_user = $banned_user;
			$current_user_id = $banned_user['User']['id'];
			$ban_record = $this->BlockedUserFrontpage->find("BlockedUserFrontpage.user_id = $current_user_id");
			$status = $banned_user['User']['status'];
			
			if ($overload_id == $current_user_id) {
				$temp_user['User']['reason'] = $overload_reason;
			} else {
				$temp_user['User']['reason'] = $ban_record['BlockedUserFrontpage']['reason'];
			}
			if ($status == 'locked') {
				if (empty($ban_record)) {
					$default_reason = "You have violated our Terms of Service. ";
					if ($overload_id == "" && $overload_reason == "") {
						$temp_user['User']['reason'] = $default_reason;
					} else {
						if ($overload_id == $current_user_id) {
							$temp_user['User']['reason'] = $overload_reason;
						}
					}
				} else {
					$temp_user['User']['reason'] = $ban_record['BlockedUserFrontpage']['reason'];
				}
			}
			
			array_push($return_array, $temp_user);
		}
		return $return_array;
	}
	
	/**
	* Remove frontpage banned user
	**/
	function remove_banned_user_frontpage($banned_id) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		
		$ban_record = $this->BlockedUserFrontpage->find("BlockedUserFrontpage.user_id = $banned_id");
		$ban_id = $ban_record['BlockedUserFrontpage']['id'];
		
		$this->BlockedUserFrontpage->del($ban_id);
        $this->BlockedUserFrontpage->mc_connect();
		$this->BlockedUserFrontpage->mc_delete('home_projects');
        $this->BlockedUserFrontpage->mc_close();
		$banned_users = $this->set_banned_users_frontpage();
		
		$this->set('data', $banned_users);
		$this->set('default_name', "");
		$this->set('default_reason', "");
		$this->render('render_banned_users_frontpage_ajax', 'ajax');
	}

	/**
	* Handles user/ip banning
	**/
	function ip_info($user_id) {	
		$this->autoRender = false;
		$this->User->id = $user_id;
		$user = $this->User->read();

        $this->ViewStat->recursion = -1;
		$stats = $this->ViewStat->findAll("ViewStat.user_id = $user_id", "DISTINCT user_id, INET_NTOA(ipaddress) ipaddress",'ViewStat.timestamp DESC');
		$status = $user['User']['status'];
		
		$this->set('user_name', $user['User']['username']);
		$this->set('status', $status);
		$this->set('data', $stats);
		$this->render('ip_info');
	}
	
	/**
	* Handles connected user's ip banning
	**/
	function connected_ip($user_id) {	
		$this->autoRender = false;
				
		$stats = $this->ViewStat->findAll("ViewStat.user_id = $user_id", "DISTINCT user_id, ipaddress");
		$ips_array = Array();
		
		foreach ($stats as $current_stat) {
			$temp_stat = $current_stat;
			array_push($ips_array,$temp_stat['ViewStat']['ipaddress']);
		}
		$ips_list = implode(',',$ips_array);
		$conditions = "BlockedIp.ip  in (".$ips_list.") ";
		$final_ips = $this->BlockedIp->findAll($conditions);
		
		$this->set('user_id', $user_id);
		$this->set('data', $final_ips);
		$this->render('connected_ip');
	}
	
	function remove_connected_ban_ip($ip_id,$user_id) {
		$this->autoRender = false;
		$this->BlockedIp->del($ip_id);
		
		$stats = $this->ViewStat->findAll("ViewStat.user_id = $user_id", "DISTINCT user_id, ipaddress");
		$ips_array = Array();
		
		foreach ($stats as $current_stat) {
			$temp_stat = $current_stat;
			array_push($ips_array,$temp_stat['ViewStat']['ipaddress']);
		}
		$ips_list = implode(',',$ips_array);
		$conditions = "BlockedIp.ip  in (".$ips_list.") ";
		$final_ips = $this->BlockedIp->findAll($conditions);
		
		$this->set('user_id', $user_id);
		$this->set('data', $final_ips);
		$this->render('render_connected_ips_ajax', 'ajax');
	}
	
	/**
	* Helper for displaying the flaggers of a project
	**/
	function expand_flaggers($project_id) {
		$this->autoRender = false;
		$projects = $this->Project->findAll("Project.id = $project_id", null, null, null, 1, null, "all", 1);
		$project = $projects[0];
		
		$this->modelClass = "Flagger";
		$this->Pagination->show = 20;
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "Flagger", 
							"direction"=> "DESC", "url" => "/administration/render_project_flaggers/$project_id");
							
		list($order,$limit,$page) = $this->Pagination->init("Flagger.project_id = $project_id", Array(), $options);
		$flaggers = $this->Flagger->findAll("Flagger.project_id = $project_id", null, $order, $limit, $page);
		
		$final_flaggers = Array();
		foreach ($flaggers as $flagger) {
			$temp_flagger = $flagger;
			$user_id = $flagger['Flagger']['user_id'];
			$current_user = $this->User->find("User.id = $user_id");
			if (empty($current_user)) {
				$temp_flagger['User']['username'] = "";
			} else {
				$temp_flagger['User']['username'] = $current_user['User']['username'];
			}
			array_push($final_flaggers, $temp_flagger);
		}
		
		$this->set('data', $final_flaggers);
		$this->set('project', $project);
		$this->render('expand_flaggers');
	}
	
	/**********************************ADMIN DEFINED TAGS***************************************/
	function predefined_tags($mode = "active") {
		$this->autoRender = false;
		$errors = Array();
		
		$this->set_admin_tags($mode);
		$this->set('errors', $errors);
		$this->set('mode', $mode);
		$this->render('admin_tags');
	}
	
	function predefined_tags_ajax($mode) {
		$this->autoRender = false;
		$errors = Array();
		
		$this->set_admin_tags($mode);
		$this->set('errors', $errors);
		$this->set('mode', $mode);
		$this->render('admin_tags_ajax', 'ajax');
	}
	
	function add_admin_tag($mode) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserId();
		$errors = Array();
		
		$tag_data = $this->data;
		$tag_name = $tag_data['Admin']['tag'];
		
		if (empty($tag_name)) {
			array_push($errors, "Please enter a valid tag.");
		} else {
			$tag_record = $this->Tag->find("Tag.name = '$tag_name'");
			if (empty($tag_record)) {
				$tag_info = 
					Array('Tag' => Array('id' => null, 'name' => $tag_name));
				$this->Tag->save($tag_info, false);
				$tag_record_id =  $this->Tag->getLastInsertID();
			} else {
				$tag_record_id = $tag_record['Tag']['id'];
			}
			
			$admin_tag_record = $this->AdminTag->findAll("AdminTag.tag_id = $tag_record_id");
			if (empty($admin_tag_record)) {
				$admin_tag_info = Array('AdminTag' => Array('id' => null, 'user_id' => $user_id, 'tag_id' => $tag_record_id, 'status' => $mode));
				$this->AdminTag->save($admin_tag_info);
			} else {
				array_push($errors, "An admin tag with that name already exists. Check both active and inactive tags.");
			}
		}
		
		$this->set_admin_tags($mode);
		$this->set('errors', $errors);
		$this->set('mode', $mode);
		$this->render('admin_tags_ajax', 'ajax');
	}
	
	function set_admin_tags($mode) {
		$admin_tags = $this->AdminTag->findAll("AdminTag.status = '$mode'");
		$this->set('admin_tags', $admin_tags);
	}
	
	function remove_admin_tag($mode, $tag_id) {
		$this->autoRender = false;
		$errors = Array();
		$this->AdminTag->del($tag_id);
		
		$this->set_admin_tags($mode);
		$this->set('errors', $errors);
		$this->set('mode', $mode);
		$this->render('admin_tags_list_ajax', 'ajax');
	}
	
	function set_admin_tag_status($status, $tag_id) {
		$this->autoRender = false;
		$errors = Array();
		$this->AdminTag->set_status($tag_id, $status);
		
		if ($status == 'active') {
			$mode = "inactive";
		} else {
			$mode = "active";
		}
		
		$this->set_admin_tags($mode);
		$this->set('errors', $errors);
		$this->set('mode', $mode);
		$this->render('admin_tags_list_ajax', 'ajax');
	}
	/**********************************ADMIN KARMA INTERFACE************************************/
	/**
	* Karma Admin Interface
	**/
	function karma() {
		$this->autoRender = false;
		
		$this->modelClass = "KarmaRank";
		$options = Array("sortBy"=>"rank", "sortByClass" => "KarmaRank", 
							"direction"=> "DESC");
							
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
		$karma_ranks = $this->KarmaRank->findAll("", null, $order, 30, $page);
		
		$this->modelClass = "KarmaEvent";
		$options = Array("sortBy"=>"effect", "sortByClass" => "KarmaEvent", 
							"direction"=> "DESC");
							
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
		$karma_events = $this->KarmaEvent->findAll("", null, $order, 30, $page);
		
		$this->set_karma();
		$this->set('karma_ranks', $karma_ranks);
		$this->set('karma_events', $karma_events);
		$this->render('karma_admin');
	}
	
	/**
	* Increases the spread between karma ranks
	**/
	function increase_spread() {
		$spread = $this->KarmaSetting->find("KarmaSetting.name = 'spread'");
		$offset = $this->KarmaSetting->find("KarmaSetting.name = 'offset'");
		$increment = 500;
		
		$current_spread = $spread['KarmaSetting']['value'];
		$current_offset = $spread['KarmaSetting']['value'];
		
		$final_spread = $current_spread + $increment;
		$spread_id = $spread['KarmaSetting']['id'];
		$this->KarmaSetting->id = $spread_id;
		$spread_record = $this->KarmaSetting->read();
		$this->KarmaSetting->saveField("value", $final_spread);
		
		$this->calculate_ratings($final_spread, $current_offset);
		$this->redirect('/administration/karma');
	}
	
	/**
	* Decreases the spread between karma ranks
	**/
	function decrease_spread() {
		$spread = $this->KarmaSetting->find("KarmaSetting.name = 'spread'");
		$offset = $this->KarmaSetting->find("KarmaSetting.name = 'offset'");
		$increment = -500;
		
		$current_spread = $spread['KarmaSetting']['value'];
		$current_offset = $spread['KarmaSetting']['value'];
		
		$final_spread = $current_spread + $increment;
		$spread_id = $spread['KarmaSetting']['id'];
		$this->KarmaSetting->id = $spread_id;
		$spread_record = $this->KarmaSetting->read();
		$this->KarmaSetting->saveField("value", $final_spread);
		
		$this->calculate_ratings($final_spread, $current_offset);
		$this->redirect('/administration/karma');
	}
	
	/**
	* Increases the offset for the highest karma rank
	**/
	function increase_offset() {
		$spread = $this->KarmaSetting->find("KarmaSetting.name = 'spread'");
		$offset = $this->KarmaSetting->find("KarmaSetting.name = 'offset'");
		$increment = 100;
		
		$current_spread = $spread['KarmaSetting']['value'];
		$current_offset = $offset['KarmaSetting']['value'];
		
		$final_offset = $current_offset + $increment;
		$offset_id = $offset['KarmaSetting']['id'];
		$this->KarmaSetting->id = $offset_id;
		$offset_record = $this->KarmaSetting->read();
		$this->KarmaSetting->saveField("value", $final_offset);
		
		$this->calculate_ratings($current_spread, $final_offset);
		$this->redirect('/administration/karma');
	}
	
	/**
	* Decreases the offset for the lowest karma rank
	**/
	function decrease_offset() {
		$spread = $this->KarmaSetting->find("KarmaSetting.name = 'spread'");
		$offset = $this->KarmaSetting->find("KarmaSetting.name = 'offset'");
		$increment = -100;
		
		$current_spread = $spread['KarmaSetting']['value'];
		$current_offset = $offset['KarmaSetting']['value'];
		
		$final_offset = $current_offset + $increment;
		$offset_id = $offset['KarmaSetting']['id'];
		$this->KarmaSetting->id = $offset_id;
		$offset_record = $this->KarmaSetting->read();
		$this->KarmaSetting->saveField("value", $final_offset);
		
		$this->calculate_ratings($current_spread, $final_offset);
		$this->redirect('/administration/karma');
	}
	
	/**
	* Calculate the karma ratings of an user
	**/
	function calculate_ratings($current_spread, $current_offset) {	
		$this->modelClass = "KarmaRank";
		$options = Array("sortBy"=>"id", "sortByClass" => "KarmaRank", 
							"direction"=> "DESC");

		$karma_ranks = $this->KarmaRank->findAll("", null);
		foreach ($karma_ranks as $karma_rank) {
			$rank_id = $karma_rank['KarmaRank']['id'];
			$rank_rating = $rank_id * $current_spread + $current_offset;
			$this->KarmaRank->id = $rank_id;
			$current_rank = $this->KarmaRank->read();
			$this->KarmaRank->saveField("rating_cap", $rank_rating);
		}
	}
	
	/**
	* Change the name of a karma rank
	**/
	function edit_rank_name($rank_id) {
        $this->autoRender=false;
        $this->KarmaRank->id = $rank_id;
        $karma_rank = $this->KarmaRank->read();

		if (empty($karma_rank))
			$this->__err();

        if (isset($this->params['form']['rank_name'])) {
            $new_name = $this->params['form']['rank_name'];

			if($this->KarmaRank->saveField('name', $new_name)) {
				$this->set('rank_name', $new_name);
                $this->render('rank_name_ajax','ajax');
                exit();
            }
        } else {
			$this->set('rank_name',$project['KarmaRank']['name']);
			$this->render('rank_name_ajax','ajax');
			exit();
		}
	}
	

	/**
	* Sets the permissions for a certain rank of karma
	**/
	function set_rank_permission($rank_id, $level, $priv, $option) {
		$this->autoRender=false;
        $this->KarmaRank->id = $rank_id;
        $karma_rank = $this->KarmaRank->read();

		if (empty($karma_rank))
			$this->__err();
		
		if ($priv == 'canTag') {
			$this->KarmaRank->saveField("canTag", $option);
			$karma_rank['KarmaRank']['canTag'] = $option;
		}
		if ($priv == 'canComment') {
			$this->KarmaRank->saveField("canComment", $option);
			$karma_rank['KarmaRank']['canComment'] = $option;
		}
		if ($priv == 'canUpload') {
			$this->KarmaRank->saveField("canUpload", $option);
			$karma_rank['KarmaRank']['canUpload'] = $option;
		}
		if ($priv == 'canLogIn') {
		}
		
		$this->set('rank', $karma_rank);
		$this->render('rank_permissions_ajax', 'ajax');
	}
	
	/**
	* Changes the offest and spread of entire karma system
	**/
	function set_karma() {
		$spread = $this->KarmaSetting->find("KarmaSetting.name = 'spread'");
		$offset = $this->KarmaSetting->find("KarmaSetting.name = 'offset'");
		$this->set('current_spread', $spread['KarmaSetting']['value']);
		$this->set('current_offset', $offset['KarmaSetting']['value']);
	}
	
	function notifications() {
		if(!empty($this->data)) {
			$this->data['is_admin'] = 1;
            $this->data['negative'] = 1;
            $this->Notification->NotificationType->save($this->data);
		}
		
		$notifications = $this->Notification->NotificationType->find('all', 
							array('conditions' => array('is_admin' => 1), 'order' => 'type ASC'));
		$this->set('notifications', $notifications);
	}
	
	function edit_notif($what, $id) {
		$new_value = $this->params['form']['value'];
		$this->Notification->NotificationType->id = $id;
		$record = $this->Notification->NotificationType->read(array('type', 'template'));
		
		//save type name
		if($what == 'name') {
			if(empty($new_value) || !
				$this->Notification->NotificationType->saveField('type', $new_value)) {
				//echo old type name
				echo $record['NotificationType']['type'];
			}
			else {
				echo $new_value;
			}
		}
		//save template
		else if($what == 'template') {
			if($this->Notification->NotificationType->saveField('template', $new_value)) {
				echo $new_value;
			}
			else {
				//echo old template
				echo $record['NotificationType']['template'];
			}
		}
		exit;
	}

    //update notification remark / negative field
    /*function update_notif_remark($id) {
        $negative = 0;
        if($this->params['url']['remark']) {
            $negative = 1;
        }
		$this->Notification->NotificationType->id = $id;
		$this->Notification->NotificationType->saveField('negative', $negative);
        exit;
    }*/

    function send_bulk_notifications() {
        if(!empty($this->data)) {
            $users = $this->Notification->addBulkNotifications($this->data['usernames'], $this->data['text']);
            $this->set('users', $users);
        }
    }

    function memcache_stat() {
        $memcache = new Memcache();
		$memcache->addServer(MEMCACHE_SERVER, MEMCACHE_PORT);
        $version = $memcache->getVersion();
        echo 'Memcache Version: ' . $version . '<br/>';
        echo 'Connecting to Memcache Server: ' . MEMCACHE_SERVER .' Port: '. MEMCACHE_PORT . '<br/>';
        echo '<pre>';
        print_r($memcache->getExtendedStats());
        echo '</pre>';
        exit;
    }
}
?>