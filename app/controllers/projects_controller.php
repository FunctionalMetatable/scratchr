<?php

class ProjectsController extends AppController {

    var $uses = array('Gallery', 'RemixedProject', 'IgnoredUser', 'TagFlag', 'Mpcomment','Project','Tagger','FeaturedProject', 'ProjectFlag', 'User','Pcomment','ViewStat','ProjectTag', 'Tag','Lover', 'Favorite', 'Downloader','Flagger', 'Notification', 'ProjectShare', 'ProjectSave', 'GalleryProject');
    var $components = array('RequestHandler','Pagination', 'Email', 'PaginationSecondary','Thumb');
    var $helpers = array('Javascript', 'Ajax', 'Html', 'Pagination');

    /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
        // $queryEnabledActions = Array("search","delcomment","explore","view", "moreprojects", "lovers", "favoriters", "taggers");
        // if (!in_array($this->params['action'], $queryEnabledActions))
        //    if (count($this->params['url']) > 1)
        //        $this->__err(); //let's not process queries for other actions
		if ($this->action != 'view') {
			parent::beforeFilter();
			$this->checkReferer();
		}
		$this->set('content_status', $this->getContentStatus());
    }

     //checks the http referer of the site to determine if it matches the server name of the current scratchr page
    function checkReferer() {
	$referer_url = strtolower(env('HTTP_REFERER'));
	$referer_url = str_replace("http://", "", $referer_url);
	$referer_url = str_replace("https://", "", $referer_url);
	$url = env('SERVER_NAME');
	$url = strtolower($url);
	$pos = strpos($referer_url, $url);
	if ($pos === false && !$referer_url == "") {
		echo "Referrer: $referer_url, URL: $url";
		$this->cakeError('error404');
	} else {
		//TODO
	}
    }
    
    /**
     * Renders project controller error page
	 * Overrides AppController::__err()
     */
    function __err() {
        $this->render('perror');
        die;
    }


	function load($urlname, $pid) {
		$this->set('urlname', $urlname);
		$this->set('pid', $pid);
	}


    /**
     * Admin index
     */
    function admin_index() {
		$this->__err();
		/* TODO: authentication for admins
        $this->set('users',$this->User->findAll());
        $this->set('projects', $this->Project->findAll());
        $this->set('comments', $this->Pcomment->findAll());
		*/
    }


	/**
	 * Admin Feature a project
	 */
	function feature($urlname=null) {
		$user_id = $this->getLoggedInUserID();
		$this->checkPermission('feature_projects');
		if (!$user_id && !$this->isAdmin()) $this->cakeError('error404');

		if (!isset($this->params['form']['urlname']) ||
			!isset($this->params['form']['pid']))
			exit;

		$project_id = $this->params['form']['pid'];
		$urlname = $this->params['form']['urlname'];
		$this->Project->id = $project_id;
		$this->Project->bindUser();
		$project = $this->Project->read();
		if(empty($project)) $this->cakeError('error404');

		$this->FeaturedProject->id=null;
		$this->FeaturedProject->save(Array('FeaturedProject'=>Array('project_id'=>$project_id)));

		$this->check_project_flag($user_id, $project_id);
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		
		if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
			$isCensored = true;
		} else {
			$isCensored = false;
		}
		
		$featured_project_id = $this->FeaturedProject->getLastInsertID();
		$featured_project = $this->FeaturedProject->find("FeaturedProject.id = $featured_project_id");
		$featured_time = $featured_project['FeaturedProject']['timestamp'];
		
		$this->set_project_flag_timestamp($project_id, "featured", $featured_time);
		$this->ProjectFlag->set_feature_admin($flag_id, $user_id);
		$this->set_admin_name($project_id, $user_id);
		$this->set_admin_time($project_id, $featured_time);
		$this->set('urlname',$urlname);
		$this->set('isFeatured', true);
		$this->set('isCensored', $isCensored);
		$this->set('status', $project['Project']['status']);
		$this->set('flags', $final_flags);
		$this->set('project_id', $project_id);
		$this->set('isFeatured', $this->FeaturedProject->hasAny("project_id = $project_id"));
		$this->render('project_set_attribute_ajax', 'ajax');
		return;
	}
	
	/**
	 * Admin deFeature a project
	 */
	function defeature($urlname=null) {
		$user_id = $this->getLoggedInUserID();
		$this->checkPermission('feature_projects');
		if (!$user_id && !$this->isAdmin())
			exit;

		if (!isset($this->params['form']['urlname']) ||
			!isset($this->params['form']['pid']))
			exit;

		$project_id = $this->params['form']['pid'];
		$urlname = $this->params['form']['urlname'];
		$this->Project->id = $project_id;
		$this->Project->bindUser();
		$project = $this->Project->read();
		if(empty($project)) $this->cakeError('error404');
		
		$featured_project = $this->FeaturedProject->find("project_id = $project_id", NULL, "FeaturedProject.id DESC");
		if (!empty($featured_project))
			$this->FeaturedProject->del($featured_project["FeaturedProject"]["id"]);

		$this->check_project_flag($user_id, $project_id);
	
		$final_flags = $this->ProjectFlag->find("project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		
		if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
			$isCensored = true;
		} else {
			$isCensored = false;
		}
		
		$this->set_project_flag_timestamp($project_id);
		$this->ProjectFlag->set_admin($flag_id, $user_id);
		$this->set_admin_name($project_id);
		$this->set_admin_time($project_id);
		$this->set('isFeatured', true);
		$this->set('urlname',$urlname);
		$this->set('isCensored', $isCensored);
		$this->set('status', $project['Project']['status']);
		$this->set('flags', $final_flags);
		$this->set('project_id', $project_id);
		$this->set('isFeatured', $this->FeaturedProject->hasAny("project_id = $project_id"));
		$this->render('project_set_attribute_ajax', 'ajax');
		return;
	}


    /**
     * Ajax-updates the title for the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function name($urlname, $pid) {
	    $this->exitOnInvalidArgCount(2);
		if (!$this->RequestHandler->isAjax())
			$this->cakeError('error404');
		if (!$this->activeSession())
			$this->cakeError('error404');

        $this->autoRender=false;

        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();

		if (empty($project) || ($project['User']['urlname'] !== $urlname))
			$this->cakeError('error404');

		if (!$this->isAdmin())
			if (!$this->activeSession($project['User']['id']))
				$this->cakeError('error404');

		// save new name
		// by default the default the scriptaculous library puts form values for Ajax.EditInPlace fields
		// in [form][value] unless specified otherwise in the javascript definition
		$inputText = (isset($this->params['form']['name'])) ? $this->params['form']['name'] : null;
		if ( $inputText ) {
			$newtitle =  $inputText ;
			if(isInappropriate($newtitle))
			{
			    $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$this->notify('inappropriate_ptitle', $user_id,
					array('project_id' => $pid));
			}
			else
			{
				if ($this->Project->saveField('name',$newtitle)) {
				   	$this->set('ptitle', $newtitle); // note: 'title' is pre-defed var for cake layout
					$this->render('projecttitle_ajax', 'ajax');  // alternative: echo $newTitle
					return;// suppresses display of page generation time i.e. <!--0.129.s-->
				}
			}
		}
        $this->set('ptitle', $project['Project']['name']);
        $this->render('projecttitle_ajax','ajax');
        return;
    }

    /**
     * Comment action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function comment($urlname=null, $pid=null, $comment_id=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		if(empty($project)) $this->cakeError('error404');
		
		$commenter_id = $this->getLoggedInUserID();
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$logged_id = $this->getLoggedInUserID();
		$project_owner_id = $project['User']['id'];
		$errors = Array();
		
		$isLocked = $this->check_locked($pid);
		
		if ($isLocked) exit();
		
        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();
	
        if (!empty($this->params['form']) || $comment_id!=null) {
            if ($commenter_id) {
				if($comment_id==null)
				{
					$comment = htmlspecialchars($this->params['form']['pcomment_textarea']);
				}
				else
				{
					$comment = htmlspecialchars($this->params['form']['pcomment_reply_textarea'.$comment_id]);
				}

				// SPAM checking
				$possible_spam = false;
				$excessive_commenting = false;
				$days = COMMENT_SPAM_MAX_DAYS;
				$max_comments = COMMENT_SPAM_CLEAR_COMMENTS;
				$time_limit = COMMENT_SPAM_CLEAR_MINUTES;
				$recent_comments_by_user = $this->Pcomment->findAll("Pcomment.user_id =  $commenter_id AND Pcomment.created > now() - interval $time_limit minute AND Pcomment.project_id = $pid");
				if(sizeof($recent_comments_by_user)>$max_comments)
				{
				  $excessive_commenting = false;
				}
				$nowhite_comment = ereg_replace("[ \t\n\r\f\v]{1,}", "[ \\t\\n\\r\\f\\v]*", $comment);
				$similar_comments = $this->Pcomment->findAll("Pcomment.content RLIKE '".$nowhite_comment."' AND Pcomment.created > now() - interval $days  day AND Pcomment.user_id = $commenter_id");
				preg_match_all("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $comment, $url_matches);
				for($i=0; $i<count($url_matches[0]); $i++)
				{
				  $url_to_check = $url_matches[0][$i];
				  if(sizeof($this->Pcomment->findAll("Pcomment.content LIKE '%".$url_to_check."%' AND Pcomment.created > now() - interval $days  day AND Pcomment.user_id = $commenter_id"))>1)
				  {
				      $possible_spam = true;
				  }
				}
				if(sizeof($similar_comments)>$max_comments)
				{
				    $possible_spam = true;
				}

				if(isInappropriate($comment)) {
					$vis = 'censbyadmin';
					$this->notify('inappropriate_pcomment', $commenter_id,
									array('project_id' => $pid,
									'project_owner_name' => $urlname),
									array($comment)
								);
				} else {
					$vis = 'visible';
				}
				if($comment_id==null)
				{
					$comment_id = 0;
				}
				
				$comment_length = strlen($comment);
				if($possible_spam)
				{
				    array_push($errors, "Sorry, you have posted a very similar message recently.");
				}
				else if($excessive_commenting)
				{
				  array_push($errors, "Take a break and try posting the comment in a few moments.");
				}
				else if ($comment_length < 2) {
					array_push($errors, "Comment too short. ");
				} elseif ($comment_length > MAX_COMMENT_LENGTH) {
					array_push($errors, "Comments cannot exceed 500 characters.");
				} else {
					$duplicate_record = $this->Pcomment->find("Pcomment.project_id = $pid AND Pcomment.user_id = $commenter_id AND Pcomment.content = '$comment'");
					
					if (empty($duplicate_record)) {
						$duplicate = false;
					} else {
						$original = $duplicate_record['Pcomment']['timestamp'];
						$today = time(); /* Current unix time */
						$since = $today - strtotime($original);
						if ($since < 15) {
							$duplicate = true;
						} else {
							$duplicate = false;
						}
					}
					
					if ($duplicate) {
					} else {
						$new_pcomment = array('Pcomment'=>array('id' => null, 'project_id'=>$pid, 'user_id'=>$commenter_id, 'content'=>$comment, 'comment_visibility' => $vis, 'reply_to_id'=>$comment_id, 'created' => date("Y-m-d G:i:s") ));
						$this->Pcomment->save($new_pcomment);
					}
				}
            }
        } else {
			array_push($errors, "Please enter a valid comment.");
		}

		$this->Pcomment->bindUser();
		$commenter_userrecord = $this->User->find("id = $commenter_id");
		$commenter_username = $commenter_userrecord['User']['username'];
		$user_id = $project['Project']['user_id'];
		$user_record = $this->User->find("id = $user_id");
		$notify_pcomment = $user_record['User']['notify_pcomment'];
		if($notify_pcomment && $urlname !== $commenter_username && $vis=='visible')
		{
			$puser_id = $project['Project']['user_id'];
			$user_record = $this->User->find("id = $puser_id");
			$username = $user_record['User']['username'];
			$project_title = htmlspecialchars($project['Project']['name']);
			
			//if comment by ignored user do not send notification
			$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.user_id = $commenter_id AND IgnoredUser.blocker_id = $project_owner_id");
			if ($ignore_count == 0) {
				//if user is not commenting on his own project
				if($logged_id != $puser_id) {
					$this->notify('new_pcomment', $puser_id,
									array('project_id' => $pid,
										'from_user_name' => $commenter_username)
							);
				}
			}
		}
		
		$final_comments = $this->set_comments($pid, $project_owner_id, $isLogged);
		$this->set_comment_errors($errors);

		$isLogged = $this->isLoggedIn();
		$this->set('isLogged',$isLogged);
		$this->set('pid', $pid);
		$this->set('project_id', $pid);
		$this->set('isProjectOwner', $logged_id == $project['User']['id']);
		$this->set('isMine', $logged_id == $project['User']['id']);
		$this->set('comments',$final_comments);
		$this->set('urlname', $urlname);
		$this->render('projectcomments_ajax', 'ajax');
		return;
    }
    
	/** Marks a comment as inappropriate (AJAX)
	* @param string $urlname => user url
	* @param int $pid => project id
	*/
	function markcomment($pid, $comment_id) {
		$this->autoRender=false;	
		$isAdmin = $this->isAdmin();
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();

		if (!$isLogged) {
			$this->cakeError('error404');
		}

		$this->Project->bindUser();
		$this->Project->id=$pid;
		
		$project = $this->Project->read();
		$urlname = $project['User']['urlname'];

		if (empty($project))
			$this->cakeError('error404'); 

		$this->Pcomment->id = $comment_id;
		$this->Pcomment->bindUser();
		$comment = $this->Pcomment->read();

		$project_creator = $project['User']['username'];
		$creator_id = $comment['Pcomment']['user_id'];
		//logged in user is the comment creator, and is not allowed to flag his own comment
		if($user_id == $creator_id) {
			return false;
		}
		$content = $comment['Pcomment']['content'];
		$isMine = ($user_id == $project['User']['id']);
		$creator = $this->User->find("User.id = '$creator_id'");
		$creatorname = $creator['User']['username'];
		$userflagger = $this->User->find("User.id = '$user_id'");
		$flaggername = $userflagger['User']['username'];
		$pname = htmlspecialchars($project['Project']['name']);

        $mpcomment_record = $this->Mpcomment->findCount("user_id = $user_id AND comment_id = $comment_id");
		//checks to see if this user has already marked this comment previously
		if ($mpcomment_record == 0) {
			$this->Mpcomment->save(array('Mpcomment'=>array('id'=>null, 'user_id' => $user_id, 'comment_id' => $comment_id)));
			/*
			$msg = "User '$flaggername' has flagged this comment by '$creatorname':\n$content\nhttp://scratch.mit.edu/projects/$project_creator/$pid";
			$subject= "Flagged comment under '$pname'";
			$this->Email->email('scratch-feedback@media.mit.edu',  $flaggername, $msg, $subject, 'scratch-caution@media.mit.edu', $userflagger['User']['email']);
			*/
		}
		
		//checks to see if the comment has been flagged too many times
		$max_count = NUM_MAX_COMMENT_FLAGS;
		$stringwflaggernames = "";
		$inappropriate_count = $this->Mpcomment->findCount("comment_id=$comment_id");
		if ($inappropriate_count > $max_count || $isMine || $isAdmin) {
			// Only do the deletion when it's the owner of the project flagging it
			if ($isMine) {
				$this->Pcomment->saveField("comment_visibility", "delbyusr") ;
				$subject= "Comment deleted because it was flagged by creator of '$pname'";
				$msg = "Comment by '$creatorname' deleted because it was flagged by the project owner:\n$content\nhttp://scratch.mit.edu/projects/$project_creator/$pid";
			} elseif ($isAdmin) {
				$this->Pcomment->saveField("comment_visibility", "delbyadmin") ;
				$subject= "Comment deleted because it was flagged by an admin";
				$msg = "Comment by '$creatorname' deleted because it was flagged by an admin:\n$content\nhttp://scratch.mit.edu/projects/$project_creator/$pid";
				$this->notify('pcomment_removed', $creator_id,
								array('project_id' => $pid,
									'project_owner_name' => $project_creator),
									array($content));
			}
			if ($inappropriate_count > $max_count) {
				$this->Mpcomment->bindUser();
				$allflaggers = $this->Mpcomment->findAll("comment_id=$comment_id");
				foreach ($allflaggers as $flagger) {
					$stringwflaggernames .= $flagger['User']['username'] . ",";
				}
				$subject = "Attention: more than $max_count users have flaggeed $creatorname's comment on '$pname'";
				$msg = "Users '$stringwflaggernames' have flagged this comment by '$creatorname':\n$content\n http://scratch.mit.edu/projects/$project_creator/$pid";
			}
			$this->Email->email(REPLY_TO_FLAGGED_PCOMMENT,  $flaggername, $msg, $subject, TO_FLAGGED_PCOMMENT, $userflagger['User']['email']);
		}
		
		$final_comments = Array();
		$this->set('urlname', $urlname);
		$this->set('isLogged', $isLogged);
		$this->set('comments', $final_comments);
		$this->set('comment', $this->Pcomment->find("Pcomment.id = $comment_id"));	
		$this->set('pid', $pid);
		$this->set('isMine', $user_id == $project['User']['id']);
		$this->set('mpcomments',$this->Mpcomment->findAll("user_id = $user_id"));
		$this->render('projectmarkcomment_ajax', 'ajax');
		return;
	}

	/** Reply to a comment (AJAX)
	* @param string $urlname => user url
	* @param int $pid => project id
	*/
	function reply_comment($urlname=null, $pid=null, $comment_id=null)
	{
		$this->set('pid', $pid);
		$this->set('urlname', $urlname);
		$this->set('comment_id', $comment_id);
		$this->render('reply_comment_ajax', 'ajax');
	}
	
	/** Deletes a comment (AJAX)
	* @param string $urlname => user url
	* @param int $pid => project id
	*/
	function delcomment($urlname=null, $pid=null)
	{
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();

		$this->exitOnInvalidArgCount(2);
		$this->autoRender=false;

		if (empty($this->params['url']['cid']))
			$this->cakeError('error404');
			
		$comment_id = $this->params['url']['cid'];
		$user_id = $this->getLoggedInUserID();

		$this->Project->bindUser();
		$this->Project->id=$pid;
		$project = $this->Project->read();
		
		$comment = $this->Pcomment->find("Pcomment.id = $comment_id");
		$comment_owner_id = $comment['User']['user_id'];
		$project_owner_id = $project['User']['user_id'];
		$isProjectOwner = $project_owner_id == $user_id;
		$isCommentOwner = $comment_owner_id == $user_id;

		if (empty($project))
			$this->cakeError('error404');
						
		if (!($this->isAdmin() || $isProjectOwner || $isCommentOwner))
			$this->cakeError('error404');
			
		$this->Pcomment->id = $comment_id;
		if ($this->isAdmin()) {
			$this->Pcomment->saveField("comment_visibility", "delbyadmin");
		} else {
			$this->Pcomment->saveField("comment_visibility", "delbyusr");
		}
	
		$final_comments = $this->set_comments($project_id, $user_id, $isLogged);

		$this->set('urlname', $urlname);
		$this->set('isLogged', $isLogged);
		$this->set('comments', $final_comments);	
		$this->set('pid', $pid);
		$this->set('isMine', $user_id == $project['User']['id']);
		$this->render('projectcomments_ajax', 'ajax');
	}


	/**
     * Download project
     * @param string $urlname => user url
     * @parm int $pid => project id
     */
    function download($urlname=null, $pid=null) {
		$this->autoRender=false;
		$file="../webroot/static/projects/$urlname/$pid.sb";
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();
		$project_name = htmlspecialchars($project['Project']['name']);
		//check if project is censored; if so, allow admin to view
		if ($project['Project']['proj_visibility'] !== 'visible'){
			if($this->isAdmin()){
				$cen_file="../webroot/static/projects/$urlname/$pid.sb.hid";			
				header("Content-type: application/force-download");
				
				header("Content-length: ".filesize($cen_file));
				header("Content-disposition: attachment; filename=\"". $project_name .".sb\"");
				header("Content-Transfer-Encoding: Binary");
				header('Pragma: public');

				header('Expires: 0');
				header('Content-Description: Quran Download');
				ob_clean();
				flush();
				readfile($cen_file);
			}
		}else{	
			header("Content-type: application/force-download");
			
			header("Content-length: ".filesize($file));
			header("Content-disposition: attachment; filename=\"". $project_name .".sb\"");
			header("Content-Transfer-Encoding: Binary");
			header('Pragma: public');

			header('Expires: 0');
			header('Content-Description: Quran Download');
			ob_clean();
			flush();
			readfile($file);
		}
		$downloader = $this->getLoggedInUserID();
        if (!$downloader)
			exit;
			
		$downloader_record = $this->Downloader->findAll("user_id = $downloader AND project_id = $pid ");
		if (empty($downloader_record)) {
			$this->Downloader->id = null;
			$this->Downloader->save(Array("Downloader"=>array('user_id'=>$downloader, 'project_id'=>$pid)));
		}
		exit();
	}

    /**
     * Update description action the given project
     * @param string $urlname => user url
     * @parm int $pid => project id
     */
    function describe($urlname=null, $pid=null) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();

		if (empty($project) || ($project['User']['urlname'] !== $urlname))
			$this->cakeError('error404');

		if (!$this->isAdmin())
			if (!$this->activeSession($project['User']['id']))
				$this->cakeError('error404');

        if (isset($this->params['form']['description'])) {
            #$newdesc = htmlspecialchars($this->params['form']['description']);
            $newdesc = $this->params['form']['description'];
	    if(isInappropriate($newdesc))
	    {
		$user_record = $this->Session->read('User');
		$user_id = $user_record['id'];
		$this->notify('inappropriate_pdesc', $user_id,
					array('project_id' => $pid));
		}
	    else
	    {
		if($this->Project->saveField('description', $newdesc)) {
                	$this->set('pdesc', $newdesc);
                	$this->render('projectdescription_ajax','ajax');
                	return;
            	}
	    }
        }
        $this->set('pdesc',$project['Project']['description']);
        $this->render('projectdescription_ajax','ajax');
        return;
    }


    /**
     * Search action on all projects or
     * all projects by user $urlname
     * @param string $urlname => user url
     */
    function search($urlname=null) {
		$this->__err();
    }


    /**
     * Action for more projects browser
     * Query/GET params:
     * ?page=$pagenumber;
     */
    function moreprojects($urlname=null) {
        $this->exitOnInvalidArgCount(1);
        $this->modelClass = "Project";
        $user = $this->User->find("User.urlname = '$urlname'");

		if (empty($user))
			$this->__err();

		// return results for provided user
		$this->modelClass = "Project";
		$user_id = $user['User']['id'];
		
		// get content for "more project" browser
		$this->Pagination->show = 5;
		$this->modelClass = "Project";
		$options = Array("sortBy"=>"created", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"moreprojects/$urlname");
		list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id AND proj_visibility = 'visible'", Array(), $options);
		$user_projects = $this->Project->findAll("user_id = $user_id AND proj_visibility = 'visible'", null, $order, $limit, $page);
		
		$this->set("urlname", $urlname);
		$this->set('user_projects', $user_projects);
    }


    /**
     * Action for project explorer browser
     * Query/GET params:
     * ?page=$pagenumber;
     */
    function explore($urlname=null) {
        $this->exitOnInvalidArgCount(1);
        $this->modelClass = "Project";
        $user = $this->User->find("User.urlname = '$urlname'");

		if (empty($user))
			$this->cakeError('error404'); //$this->__err();

		// return results for provided user
		$this->modelClass = "Project";
		$user_id = $user['User']['id'];
		$options = Array("show"=>"10");
		$criteria = Array("user_id" => $user_id);
		list($order,$limit,$page) = $this->Pagination->init($criteria, $options);
		$data = $this->Project->findAll($criteria, NULL, $order, $limit, $page, NULL, $this->getContentStatus());
		$this->set("urlname", $urlname);
		$this->set("data", $data);
		$this->render("pexplorer");
    }


    /**
     * Love action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function loveit($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;

        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];

        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();

		$user_id = $this->getLoggedInUserID();
		$this->data['Lover']['user_id'] = $user_id;
		$this->data['Lover']['project_id'] = $pid;
		$this->data['Lover']['id'] = null;
		if ($user_id) {
			if (!$this->Lover->hasAny("project_id = $pid AND user_id = $user_id")) {
				$this->Lover->save($this->data);
				$prev_lovers_count = $project['Project']['loveit'];
				if ($prev_lovers_count == 0) {
					$prev_lovers_count = 0;
				}
				$this->Project->set_loveits($pid, $prev_lovers_count + 1);
				$this->set('just_loved', true);
				$this->set('pid', $pid);
				$this->set('urlname', $urlname);
				
			}
		}
        $this->render("projectloving_ajax", "ajax");
    }


    /**
     * Love action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function unloveit($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;

        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];

        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();

		$user_id = $this->getLoggedInUserID();
		if ($user_id) {
			$lover = $this->Lover->find("project_id = $pid AND user_id = $user_id");
			if (!empty($lover)) {
				$this->Lover->del($lover['Lover']['id']);
				$prev_lovers_count = (int)$project['Project']['loveit'];
				$new_lovers_count = $prev_lovers_count - 1;
				$this->Project->saveField('loveit', ($new_lovers_count == 0 ? null : $new_lovers_count));
				$this->set('pid', $pid);
				$this->set('urlname', $urlname);
				
			}
		}
        $this->render("projectloving_ajax", "ajax");
    }


	/**
	 * Renders pagination of users who love a project
	 * project $pid of $urlname
	 * @param string $urlname
	 * @param int $pid
	 */
	function lovers($urlname=null, $pid=null) {
		$this->exitOnInvalidArgCount(2);
		if ($urlname == null || $pid == null)
			$this->__err();

		if (!$this->isAdmin() && $urlname !== $this->getLoggedInUrlname())
			$this->__err();

		$this->Project->bindUser();
		$this->Project->id = $pid;
        $project = $this->Project->read();
        if (empty($project) || $project['User']['urlname'] !== $urlname)
            $this->cakeError('error404'); //$this->__err();

		$this->Pagination->show = 50;
		$this->modelClass = "Lover";
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "Lover", 
					"direction"=> "DESC", "url"=>"/projects/".$urlname."/".$pid."/lovers");
		list($order,$limit,$page) = $this->Pagination->init("Lover.project_id = $pid AND User.status = 'normal'", Array(), $options);
		$lovers = $this->Lover->findAll("Lover.project_id = $pid AND User.status = 'normal'", null, $order, $limit, $page, 2);
		
		$this->set('lovers',$lovers);
	}

    /**
     * Flag action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function flag($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;

        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];
		$puid = $project['Project']['user_id'];
		$creator = $this->User->find("User.id = $puid");
		$creatorname = $creator['User']['username'];
		$msgin = $this->params['form']['flagmsg'];
		$project_status = $project['Project']['status'];
		$username = $creatorname;
		$project_title = htmlspecialchars($project['Project']['name']);

		$pname = htmlspecialchars($project['Project']['name']);
		
		
        
		if (empty($project) || empty($msgin) || $project['User']['urlname'] !== $urlname)
            exit();

		$user_id = $this->getLoggedInUserID();
		$userflagger = $this->User->find("User.id = '$user_id'");
		$flaggername = $userflagger['User']['username'];

		$this->data['Flagger']['user_id'] = $user_id;
		$this->data['Flagger']['project_id'] = $pid;
		$this->data['Flagger']['creator_id'] = $creatorname;
		$this->data['Flagger']['reasons'] = $msgin;

		if ($user_id) {
			if (!$this->Flagger->hasAny("project_id = $pid AND user_id = $user_id")) {
				if ($project_status == 'notreviewed') {
					$subject= "Project '$pname' flagged";
				} else {
					$subject= "Project '$pname' flagged" . " (REVIEWED)";
				}
				$msg = "user $flaggername ($user_id) just flagged http://scratch.mit.edu/projects/$creatorname/$pid \n Reason: \n " . $msgin;
			
				$this->Email->email(REPLY_TO_FLAGGED_PROJECT,  $flaggername, $msg, $subject, TO_FLAGGED_PROJECT, $userflagger['User']['email']);
				$this->Flagger->save($this->data);
				$prev_flaggers_count = (int)$project['Project']['flagit'];
				$this->Project->saveField('flagit',($prev_flaggers_count + 1));
				$flags = $project['Project']['flagit'];
				$this->set('just_flagged', true);
				$this->set('pid', $pid);
				$this->set('urlname', $urlname);
				
				//if the number of flags on this project exceeds the current maximum, automatically censor this project
				if ($flags >= NUM_MAX_PROJECT_FLAGS && $project_status == 'notreviewed') {
					//if the project is not censored already
					if($project['Project']['proj_visibility'] == 'visible') {
						$this->Project->censor($pid, $urlname, $this->isAdmin(), $user_id);
						
						$msg = "Project *automatically censored* because it reached the maximum number of flags.\n";
						$msg .= "user $flaggername ($user_id) just flagged http://scratch.mit.edu/projects/$creatorname/$pid";
						$subject= "Project '$pname' censored";
						$this->Email->email(REPLY_TO_FLAGGED_PROJECT,  'Scratch Website', $msg, $subject, TO_FLAGGED_PROJECT, 'scratch-feedback@media.mit.edu');
						
						$this->notify('project_removed_auto', $creator['User']['id'],
										array('project_id' => $pid));
						
						//if the censored project is created N mins ago then temp block the user
						$block_time = date('Y-m-d H:i:s', strtotime('-' . BLOCK_CHECK_INTERVAL, time()));
						if($project['Project']['created'] >= $block_time) {
							//block user for X mins
							$this->User->tempblock($project['Project']['user_id']);
							$this->notify('account_lock', $project['Project']['user_id'], array());
						}
					}
				}
				else {
					$this->render("projectflagging_ajax", "ajax");
				}
			}
		}
        return;
    }




	/**
	 * Renders pagination of users who flag a project
	 * project $pid of $urlname
	 * @param string $urlname
	 * @param int $pid
	 */
	function flaggers($urlname=null, $pid=null) {
		$this->exitOnInvalidArgCount(2);
		if ($urlname == null || $pid == null)
			$this->cakeError('error404');

		if (!$this->isAdmin() && $urlname !== $this->getLoggedInUrlname())
			$this->cakeError('error404');

		$this->Project->bindUser();
		$this->Project->id = $pid;
        $project = $this->Project->read();
        if (empty($project) || $project['User']['urlname'] !== $urlname)
            $this->cakeError('error404');

		$this->modelClass = "User";
		$options = Array("url"=>"/projects/".$urlname."/".$pid."/flaggers", "show"=>50);
		$criteria = Array("project_id" => $project['Project']['id']);
		$this->Pagination->ajaxAutoDetect = false;
		list($order,$limit,$page) = $this->Pagination->init($criteria, $options);
		$this->Flagger->bindUser(); //hehe...todo: bindMoreUsers()
		$flaggers = $this->Flagger->findAll($criteria, NULL, $order, $limit, $page, 2);
		$this->set('flaggers',$flaggers);
	}

	function cflag($urlname=null, $pid=null, $cid=null) {
		$this->Email->email('comment-flagged@scratch.mit.edu', "Flagger", "Flagged $cid", "Flagged $cid");
	}

	/**
	 * Renders pagination of users who tagged a project
	 * project $pid of $urlname
	 * @param string $urlname
	 * @param int $pid
	 */
	function taggers($urlname=null, $pid=null) {
		$this->exitOnInvalidArgCount(2);
		if ($urlname == null || $pid == null)
			$this->cakeError('error404');

		/*if (!$this->isAdmin() && $urlname !== $this->getLoggedInUrlname())
			$this->__err();*/
		$users_array=array();
		$this->Project->bindUser();
		$this->Project->id = $pid;
		$project_id = $pid;
		$project = $this->Project->read();
		if (empty($project) || $project['User']['urlname'] !== $urlname)
		$this->cakeError('error404');

		$this->Pagination->show = 50;
		$this->modelClass = "ProjectTag";
		$options = Array("sortBy"=>"username", "sortByClass" => "User", 
					"direction"=> "ASC", "url"=>"/projects/".$urlname."/".$pid."/taggers");
		list($order,$limit,$page) = $this->Pagination->init("ProjectTag.project_id = $pid AND ProjectTag.user_id > 0 AND User.status = 'normal' GROUP BY tag_id", Array(), $options);
		$final_taggers = $this->ProjectTag->findAll("ProjectTag.project_id = $pid AND ProjectTag.user_id > 0 AND User.status = 'normal' GROUP BY tag_id", null, $order, $limit, $page);
		$final_taggers_count = $this->ProjectTag->findCount("ProjectTag.project_id = $pid AND ProjectTag.user_id > 0 AND User.status = 'normal' ", null, $order, $limit, $page);
		$final_results =Array();
		if($final_taggers_count > 0){
		foreach($final_taggers as $taggers)
		{
			array_push($users_array,$taggers['User']['id']);
		}
		
		$unique_users_array = array_unique($users_array);
		$allid  = implode(',',$unique_users_array);
		$this->User->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Tags' => array(
                'className' => 'Tags',
                'joinTable' => 'project_tags',
                'foreignKey' => 'user_id',
                'associationForeignKey' => 'tag_id',
				'conditions' => 'project_id='.$pid
			))));
				
		$final_results=$this->User->findAll("User.id in (".$allid.") " );
		$this->set('tags', $final_results);
		}
		else
		$this->set('tags', $final_results);
	}

	
	/*
	* markTag - marks a project_tag as inappropriate
	* $project_tag_id - project_tag id
	*
	*/
	function markTag($project_tag_id, $option = "null") {
		$this->autoRender=false;
		
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$user = $this->User->find("User.id = $user_id");
		$user_name = $user['User']['username'];
		
		$this->ProjectTag->id = $project_tag_id;
		$project_tag = $this->ProjectTag->read();
		$project_id = $project_tag['ProjectTag']['project_id'];
		
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		
		$project_name = $project['Project']['name'];
		$project_creator = $project['User']['username'];
		$project_owner_id = $project['Project']['user_id'];
		$urlname = $project['User']['urlname'];
		
		$tag_id = $project_tag['ProjectTag']['tag_id'];
		$project_tag_id = $project_tag['ProjectTag']['id'];
		$this->Tag->id = $tag_id;
		$tag = $this->Tag->read();
		$tag_name = $tag['Tag']['name'];
		
		if (!$isLogged) {
			$this->__err();
		}
		
		$mark_records = $this->TagFlag->findAll("user_id = $user_id AND tag_id = $tag_id");
		if (empty($mark_records)) {
			$info = Array('TagFlag' => Array('id' => null, 'user_id' => $user_id, 'tag_id' => $tag_id));
			$this->TagFlag->save($info);
		}
		
		$removed = false;
		if ($project_owner_id == $user_id) {
			$removed = true;
			$this->ProjectTag->del($project_tag_id);
			$subject = "Attention: the owner of the project '$project_name' has flagged the tag '$tag_name'";
			$msg = "'$tag_name' has been removed because it was flagged by project owner $user_name of \nhttp://scratch.mit.edu/projects/$project_creator/$project_id";
		} else {
			$stringwflaggernames = "";
			$mark_count = $this->TagFlag->findCount("tag_id = $tag_id") + 1;
			
			if ($mark_count > NUM_MAX_TAG_FLAGS) {
				$removed = true;
				$project_tags = $this->ProjectTag->findAll("ProjectTag.project_id = $project_id AND tag_id = $tag_id");
				
				foreach ($project_tags as $current_tag) {
					$current_id = $current_tag['ProjectTag']['id'];
					$this->ProjectTag->del($current_id);
				}
				
				$allflaggers = $this->TagFlag->findAll("tag_id = $tag_id");
				foreach ($allflaggers as $flagger) {
					$flagger_id = $flagger['TagFlag']['user_id'];
					$flagger_user = $this->User->find("User.id = $flagger_id");
					
					if (empty($flagger_user)) {
					
					} else {
						$flagger_name = $flagger_user['User']['username'];
						$stringwflaggernames .= $flagger_name . ",";
					}
				}
				$subject = "Attention: more than " . NUM_MAX_TAG_FLAGS . " users have flagged the tag '$tag_name' on $project_name";
				$msg = "Users '$stringwflaggernames' have flagged the tag-$tag_name on \nhttp://scratch.mit.edu/projects/$project_creator/$project_id";
			}
		}
		
		$project_tag = $this->set_tag($project_id, $user_id, $project_tag, 1);
		
		$this->set('removed', $removed);
		$this->set('tag_id', $tag_id);
		$this->set('tag', $project_tag);
		$this->set('urlname', $urlname);
		$this->set('pid', $project_id);
		$this->set('isMine', $user_id == $project['User']['id']);
		$this->render('mark_tag_ajax', 'ajax');
	}


	function set_tag($project_id, $user_id, $tag, $overload = 0) {
		$project_tag = $tag;
		$tag_id = $project_tag['ProjectTag']['tag_id'];
		$flagged_record = Array();
		$tagged_record = Array();
		$isLogged = $this->isLoggedIn();
		
		if ($isLogged) {
			$flagged_record = $this->TagFlag->findAll("TagFlag.user_id = $user_id AND tag_id = $tag_id");
		}
		if (empty($flagged_record)) {
			$flagged = false;
		} else {
			$flagged = true;
		}
		
		if ($isLogged) {
			$tagged_record = $this->ProjectTag->findAll("user_id = $user_id AND tag_id = $tag_id");
		}
		if (empty($tagged_record)) {
			$tagged = false;
		} else {
			$tagged = true;
		}
		
		$tag_count = $this->ProjectTag->findCount("project_id = $project_id AND tag_id = $tag_id");
		$tag_size = $this->getTagSize($tag_count);
		$project_tag['ProjectTag']['size'] = $tag_size;
		
		if ($overload == 0) {
			$project_tag['ProjectTag']['flagged'] = $flagged;
			$project_tag['ProjectTag']['tagged'] = $tagged;
		} elseif ($overload == 1) {
			$project_tag['ProjectTag']['flagged'] = true;
			$project_tag['ProjectTag']['tagged'] = $tagged;
		} elseif ($overload == 2) {
			$project_tag['ProjectTag']['flagged'] = $flagged;
			$project_tag['ProjectTag']['tagged'] = true;
		}
		return $project_tag;
	}
	
	/**
	* Tag a project with a tag that has already been used
	**/
	function upgradeTag($project_tag_id, $option = "null") {
		$this->autoRender = false;
		
		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		
		$this->ProjectTag->id = $project_tag_id;
		$project_tag = $this->ProjectTag->read();
		
		$project_id = $project_tag['ProjectTag']['project_id'];
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$urlname = $project['User']['urlname'];
		
		$tag_id = $project_tag['ProjectTag']['tag_id'];
		$this->Tag->id = $tag_id;
		$tag = $this->Tag->read();
		
		$project_tag_record = $this->ProjectTag->findAll("user_id = $user_id AND project_id = $project_id AND tag_id = $tag_id");
		if (empty($project_tag_record)) {
			$this->ProjectTag->save(array('ProjectTag' => array('id' => null, 'user_id' => $user_id, 'project_id' => $project_id, 'tag_id' => $tag_id)));
		}
		
		$project_tag = $this->set_tag($project_id, $user_id, $project_tag, 2);
		
		$this->set('removed', false);
		$this->set('tag_id', $tag_id);
		$this->set('tag', $project_tag);
		
		$this->set('urlname', $urlname);
		$this->set('pid', $project_id);
		$this->set('isMine', $user_id == $project['User']['id']);
		$this->render('mark_tag_ajax', 'ajax');
	}
	
    /**
     * Rate action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function rate($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;

        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];

        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();

        if (!empty($this->data)) {
			$user_id = $this->data['Lover']['user_id']; //hidden input field
            if ($this->activeSession($user_id)) {
				if (!$this->Vote->hasAny("project_id = $pid AND user_id = $user_id")) {
					$prev_raters_count = (int)$project['Project']['num_raters'];
					$user_rating = $this->data['Vote']['rating'];
					$this->Vote->save($this->data);
					$this->set('raters_count', $prev_raters_count + 1);
					$this->set('rating', $user_rating);
					$this->set('current_user_id', $this->data['Vote']['user_id']);
					$newRating = $this->Vote->calcNewRating($prev_raters_count, $project['Project']['rating'], $user_rating);
					$newRatersCount = $prev_raters_count + 1;
					$this->Project->save(array("rating"=>$newRating, "num_raters"=>$newRatersCount));
					$this->render("projectrating_ajax", "ajax");
				}
            }
        }
       return;
    }

    /**
     *  action on given project
     * @param string $urlnmae => user url
     * @param int $pid => project id
     */
    function favorite($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];

        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();

		$user_id = $this->getLoggedInUserID();
		$this->data['Favorite']['project_id'] = $pid;
		$this->data['Favorite']['user_id'] = $user_id;
        if ($user_id) {
			if (!$this->Favorite->hasAny("Favorite.project_id = $pid AND Favorite.user_id = $user_id")) {
				$this->Favorite->save($this->data);
				$num_favoriters = (int)$project['Project']['num_favoriters'] + 1;
				$this->Project->saveField('num_favoriters', $num_favoriters);
				$this->set('already_favorited', false);
				$this->set('just_favorited', true);
				$this->set('pid', $pid);
				$this->set('urlname', $urlname);
				$this->render("projectfavorite_ajax", "ajax");
			}
        }
        return;
    }


	/**
     * Unfavorite action on given project
     * @param string $urlnmae => user url
     * @param int $pid => project id
     */
    function unfavorite($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];

        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();

		$user_id = $this->getLoggedInUserID();
		if ($user_id) {
			$favorite = $this->Favorite->find("project_id = $pid AND user_id = $user_id");
			if (!empty($favorite)) {
				$this->Favorite->del($favorite['Favorite']['id']);
				$prev_favoriters_count = (int)$project['Project']['num_favoriters'];
				$new_favoriters_count = $prev_favoriters_count - 1;
				$this->Project->saveField("num_favoriters", ($new_favoriters_count == 0 ? null : $new_favoriters_count));
				$this->set("pid", $pid);
				$this->set("urlname", $urlname);
				$this->render("projectfavorite_ajax", "ajax");
			}
		}
        return;
    }

    /**
     * Bookmark action on given project
     * @param string $urlnmae => user url
     * @param int $pid => project id
     */
    function bookmark($urlname, $pid) {
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$pid = $project['Project']['id'];

        if (empty($project) || $project['User']['urlname'] !== $urlname)
            exit();

        if (!empty($this->data)) {
			$user_id = $this->data['Bookmark']['user_id'];
            if ($this->activeSession($user_id)) {
				if (!$this->Bookmark->hasAny("project_id = $pid AND user_id = $user_id")) {
					$this->Bookmark->save($this->data);
					$num_bookmarks = (int)$project['Project']['num_bookmarks'] + 1;
					$this->Project->saveField('num_bookmarks', $num_bookmarks);
					$this->set('already_bookmark', false);
					$this->render("projectbookmark_ajax", "ajax");
				}
            }
        }
        return;
    }


	/**
	 * Renders pagination of users who have favorited
	 * project $pid of $urlname
	 * @param string $urlname
	 * @param int $pid
	 */
	function favoriters($urlname=null, $pid=null) {
		$this->exitOnInvalidArgCount(2);
		if ($urlname == null || $pid == null)
			$this->cakeError('error404');

		if (!$this->isAdmin() && $urlname !== $this->getLoggedInUrlname())
			$this->cakeError('error404');

		$this->Project->bindUser();
		$this->Project->id = $pid;
        $project = $this->Project->read();
        if (empty($project) || $project['User']['urlname'] !== $urlname)
            $this->cakeError('error404');

		$this->modelClass = "Favorite";
		$options = Array("url"=>"/projects/".$urlname."/".$pid."/favoriters", "show"=>50);
		$criteria = Array("project_id" => $project['Project']['id']);
		$this->Pagination->ajaxAutoDetect = false;
		list($order,$limit,$page) = $this->Pagination->init($criteria, $options);
		$this->Favorite->bindUser();
		$favoriters = $this->Favorite->findAll($criteria, NULL, $order, $limit, $page);
		$this->set('favoriters',$favoriters);
	}

	/**
	* Checks to see if the project owner has locked the project
	**/
	function check_locked($project_id) {
		$this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$project_id;
		$project = $this->Project->read();
		$isLocked = $project['Project']['locked'];
		$current_user_id = $this->getLoggedInUserID();
		if ($current_user_id == $project['Project']['user_id']) {
			$isMe = true;
		} else {
			$isMe = false;
		}
		
		if ($isLocked && !$isMe) {
			return true;
		} else {
			return false;
		}
	}
    /**
     * Tag action on the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function tag($urlname=null, $pid=null) {
		$this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
		$project_id = $pid;
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		
		$isLocked = $this->check_locked($pid);
		
        if (empty($project) || $project['User']['urlname'] !== $urlname) exit();
		
		$tagger = $this->getLoggedInUserID();
        if (!$tagger) exit;
		
		if ($isLocked) {
			exit;
		} else {
			if (!empty($this->params['form']['tag_textarea']))
			{

				$tagsstring = substr($this->params['form']['tag_textarea'], 0, 30);

				$tagsarray = explode(",",$tagsstring);

				foreach ($tagsarray as $tag)
				{
					$ntag = strtolower(trim($tag));
					if (empty($ntag))
						continue;
						
					if(isInappropriate($ntag))
					{
						$user_record = $this->Session->read('User');
						$user_id = $user_record['id'];
						$this->notify('inappropriate_ptag', $user_id,
									array('project_id' => $pid,
									'project_owner_name' => $urlname),
									array($ntag));
						continue;
					}
					$this->Tag->bindProjectTag(array('project_id'=>$pid));
					$tag_record = $this->Tag->find("name = '$ntag'",null,null,2);
					if (!empty($tag_record))
					{
						if (empty($tag_record['ProjectTag']))
						{
							// create project_tag record
							$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id' => $pid, 'tag_id' => $tag_record['Tag']['id'], 'user_id' => $tagger)));
							$this->ProjectTag->id=null;
						}
					}
				else
				{
						// create tag record
						$this->Tag->save(array('Tag'=>array('name'=>$ntag)));
						$this->Tag->id=null; // otherwise things will be overridden
						$tag_id = $this->Tag->getLastInsertID();
						$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id'=>$pid, 'tag_id'=>$tag_id, 'user_id' => $tagger)));
						$this->ProjectTag->id=null;
					}
				}
			}
		}

		$project_tags = $this->ProjectTag->findAll("project_id = $project_id");
		$final_tags = Array();
		$all_tags = Array();
		$this->ProjectTag->bindTag();
		$counter = 0;
		foreach ($project_tags as $current_tag) {
			$current_tag_id = $current_tag['Tag']['id'];
			$current_tag = $this->set_tag($project_id, $user_id, $current_tag);
		
			if (in_array($current_tag_id, $all_tags)) {
			} else {
				array_push($all_tags, $current_tag_id);
				$final_tags[$counter] = $current_tag;
				$counter++;
			}
		}
		
		$this->set('isLogged', $isLogged);
		$this->set('urlname', $urlname);
		$this->set('pid', $pid);
        $this->set('tags', $final_tags);
		$this->set('isMine', $tagger == $project['User']['id']);
        $this->render('projecttags_ajax', 'ajax');
        return;
    }

	function project_locked_error() {
		$this->autoRender = false;
		$this->render('project_locked_error');
		die;
	}
	
	/**
	 * Dissociates a tag from a project
	 * Does not delete the tag if no project associated with it
	 */
	function deltag($project_id, $project_tag_id)
	{
        $this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$project_id;
        $project = $this->Project->read();
		
		if (!$this->isAdmin())
			if (!$this->activeSession($project['User']['id']))
				$this->cakeError('error404');

		$this->ProjectTag->del($project_tag_id);
		exit;
	}


	/**
	 * Deletes a project
	 * @param int $pid => project id
	 */
	 function censor() {
		$this->autoRender = false;
		$this->checkPermission('censor_projects');
		$users_permission =$this->isAnyPermission();
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (!isset($this->params['form']['urlname']) ||
			!isset($this->params['form']['pid']))
			exit;

		$pid = $this->params['form']['pid'];
		$urlname = $this->params['form']['urlname'];

		$this->Project->bindUser();
		$this->Project->id = intval($pid);
		$project = $this->Project->read("user_id");
		if (empty($project['Project']['user_id']))
			$this->cakeError('error404');
		
		$project_record = $this->Project->read();
		$puser_id = $project_record['Project']['user_id'];
		$username = $project_record['User']['username'];
		$project_id = $project_record['Project']['id'];
		$this->check_project_flag($user_id, $project_id);
		$this->set_project_flag_timestamp($project_id);
		
		$this->Project->saveField("status", "censored");
		$this->Project->saveField("proj_visibility", "censbyadmin");
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		$this->ProjectFlag->set_admin($flag_id, $user_id);

		$user_record = $this->User->find("User.id = $puser_id");

		$username = $user_record['User']['username'];
		$project_title = htmlspecialchars($project_record['Project']['name']);

		if($this->isAdmin())
		{
			$this->notify('project_removed_admin', $user_record['User']['id'],
									array('project_id' => $pid));
		}

		if ($this->isAdmin() || ($project['Project']['user_id'] == $user_id) || isset($users_permission['censor_projects']))
		{
			$this->Project->censor($pid, $urlname, $this->isAdmin(), $user_id);
			$this->setFlash(___("Project censored", true), FLASH_NOTICE_KEY);
			$this->redirect("/projects/$urlname/$pid");
		}
		
		if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
			$isCensored = true;
		} else {
			$isCensored = false;
		}
		$this->set_admin_name($pid);
		$this->set_admin_time($pid);
		$this->set('isCensored', $isCensored);
		$this->redirect('/projects/'. $username. '/' . $pid);
	 }


	/**
	 * Uncensors a project
	 * @param int $pid => project id
	 */
	 function uncensor() {
		$this->autoRender = false;
		$this->checkPermission('censor_projects');
		$users_permission =$this->isAnyPermission();
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (!isset($this->params['form']['urlname']) ||
			!isset($this->params['form']['pid']))
			exit;

		$pid = $this->params['form']['pid'];
		$urlname = $this->params['form']['urlname'];

		$this->Project->bindUser();
		$this->Project->id = intval($pid);
		$project = $this->Project->read("user_id");
		if (empty($project['Project']['user_id']))
			$this->cakeError('error404');
		
		$project_record = $this->Project->read();
		$puser_id = $project_record['Project']['user_id'];
		$project_id = $project_record['Project']['id'];
		
		$this->Project->id = intval($pid);
		$this->Project->bindUser();
		$project = $this->Project->read();
		$username = $project['User']['username'];

		$user_record = $this->User->find("id = $puser_id");
		$project_title = htmlspecialchars($project_record['Project']['name']);
		
		$this->check_project_flag($user_id, $project_id);
		$this->set_project_flag_timestamp($project_id);

		
		if($this->isAdmin())
		{
			$this->notify('project_restored', $project['Project']['user_id'],
						array('project_id' => $pid));
		}

		if ($this->isAdmin() || ($project['Project']['user_id'] == $user_id) || isset($users_permission['censor_projects']))
		{
			$this->Project->uncensor($pid, $urlname, $this->isAdmin(), $user_id);
			$this->redirect("/projects/$urlname/$pid");
		}
		
		if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
			$isCensored = true;
		} else {
			$isCensored = false;
		}
		$this->set('isCensored', $isCensored);
		$this->redirect('/projects/'. $username. '/' . $pid);
	 }

	/**
	 * Deletes a project
	 * @param int $pid => project id
	 */
	 function delete() {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (!isset($this->params['form']['urlname']) ||
			!isset($this->params['form']['pid']))
			exit;

		$pid = $this->params['form']['pid'];
		$urlname = $this->params['form']['urlname'];

		$this->Project->bindUser();
		$this->Project->id = intval($pid);
		$project = $this->Project->read("user_id");
		if (empty($project['Project']['user_id']))
			$this->cakeError('error404');

		if ($this->isAdmin() || ($project['Project']['user_id'] == $user_id))
		{
			if ($this->isAdmin()) {
				$this->hide_project($project_id, "delbyadmin");
			} else {
				$this->hide_projetct($project_id, "delbyusr");
			}
			
			$this->setFlash(___("Project deleted", true), FLASH_NOTICE_KEY);
			$this->redirect('/users/'.$this->getLoggedInUrlname());
		}
		exit;
	 }


	/**
	 * Delete action on projects
	 */
	function deleteprojects() {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();

		if (empty($this->params["form"]))
			$this->cakeError('error404');

		$formData = $this->params["form"];
		if  ($this->isAdmin() || $formData["UID"] == $user_id)
		{
			$this->User->id = $formData["UID"];
			$urlname = $this->User->field("urlname");
			if (!$urlname)
				$this->cakeError('error404');

			array_shift($formData);

			foreach ($formData as $pidstring)
			{
				$pidVals = explode('_',$pidstring);
				$pid = trim($pidVals[1]);

				if ($pid != "")
					$this->Project->remove($pid, $urlname, $this->isAdmin(), $user_id);
			}
			$this->setFlash(___("Projects deleted", true), FLASH_NOTICE_KEY);
			$this->redirect('/users/'.$urlname);
		}
		exit;
	}


    /**
     * Displays the project index page, user projects, or project page
     * @param string $urlname => url to user projects
     * @param int $pid => project id
     */
    function view($urlname=null, $pid=null) {
        	if ($pid && $urlname) {
            // TODO: make only one call to find in this clause
            // TODO: set global associations to bind model at load-time
            // TODO: get $uid from hidden field when possible / from directory lookup

            // bind comment, user, and tag models
            // $this->Project->bindBinary();
			$isLogged = $this->isLoggedIn();
			$logged_id = $this->getLoggedInUserID();
			$current_user_id = $logged_id;
			$project_id = $pid;
			
			// forgive missmatch in upper/lower case of urlname
			$usrobj = $this->User->find(array('urlname' =>  $urlname),'username');
			if ($usrobj && $usrobj['User']['username']) 
				$urlname = $usrobj['User']['username'];

			$this->Project->id=$pid;
			$content_status = $this->getContentStatus();
			$projects = $this->Project->findAll("Project.id = $project_id", null, null, null, 1, null, "all", 1);

			if (empty($projects)) {
				$this->cakeError('error',array('code'=>'404', 'message'=>'project_not_found', 'name' => __('Not Found', true)));
			} 
			else {
				$project = $projects[0];
			}
			
			$project_visibility = $project['Project']['proj_visibility'];

			if($project_visibility == "delbyusr" || ($project_visibility ==  "delbyadmin" && ! $this->isAdmin()))  {
				$this->cakeError('error',array('code'=>'404', 'message'=>'project_deleted', 'name' => __('Not Found', true)));
			}

			$project_id = $project['Project']['id'];
			$owner_id = $project['User']['id'];
			$isMine = $logged_id == $owner_id;
			
			if ($project['User']['urlname'] !== $urlname)
				$this->cakeError('error404');
			 $viewcount = $project['Project']['views'];
	   		 $client_ip = ip2long($this->RequestHandler->getClientIP());
			if ($isLogged) {
				$viewstat = array('ViewStat' => array("id" => null, "user_id" => $logged_id,"project_id" => $project_id, "ipaddress" => $client_ip));
				$this->ViewStat->save($viewstat);
				if($this->ViewStat->findCount("ipaddress = '$client_ip' && project_id = $pid")==1) {
					$viewcount++;
				}
				$this->Project->saveField('views', $viewcount);
			}
			
			$remix_count = count($this->ProjectShare->findAll("related_project_id = $pid group by project_id, user_id")) - 1;
			$this->Project->saveField("remixes", $remix_count);
			
			$final_comments = $this->set_comments($pid, $owner_id, $isLogged);
			$this->set_comment_errors(Array());

            // set project lovers info
            $already_loved = null;
            if ($current_user_id)
                $already_loved = $this->Lover->hasAny("project_id = $pid AND user_id = $current_user_id");
         

            // set project flaggers info
            $already_flagged = null;
            if ($current_user_id)
                $already_flagged = $this->Flagger->hasAny("project_id = $pid AND user_id = $current_user_id");
            $this->set('already_flagged', $already_flagged);
			$this->set('flagges_count', (int)$project['Project']['flagit']);

            // get favorites info
			$already_favorited = null;
            if ($current_user_id)
                $already_favorited = $this->Favorite->hasAny("Favorite.project_id = $pid AND Favorite.user_id = $current_user_id");
			$this->set('already_favorited', $already_favorited);
            $this->set('favorite_count', (int)$project['Project']['num_favoriters']);

            // get content for "more project" browser
			$this->Pagination->show = 5;
			$this->modelClass = "Project";
			$options = Array("sortBy"=>"created", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"moreprojects/$urlname");
			list($order,$limit,$page) = $this->Pagination->init("Project.id != $pid AND user_id = $owner_id AND proj_visibility = 'visible'", Array(), $options);
			$user_projects = $this->Project->findAll("Project.id != $pid AND user_id = $owner_id AND proj_visibility = 'visible'", null, $order, $limit, $page);
			
			if ($this->ProjectFlag->findCount("project_id = $pid") == 0) {
				$project_flags = null;
			} else {
				$project_flags = $this->ProjectFlag->find("project_id = $pid");
			}
			

            // set generic project info
			if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
				$isCensored = true;
			} else {
				$isCensored = false;
			}
			
			//sets the last altered admin's name
			$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $pid");
			
			$admin_name = $this->get_admin_name($pid);
			
		// setting related work/projects strings
			$this->set('related_username', $project['Project']['related_username']);
			if($project['Project']['related_username'])  {
				$related_user = $this->User->find(array('username' => $project['Project']['related_username']));
				$this->set('related_user_id', $related_user['User']['id']);
				$this->set('related_project_id', $project['Project']['related_project_id']);
				$condition = "user_id = '" . $project['User']['id'] . "' AND project_id = '$pid' AND related_user_id != $owner_id AND related_username != '" .  $project['Project']['related_username'] . "'";
				$relprojects = $this->ProjectShare->findAll($condition, "id, related_username, related_user_id, related_project_id, related_project_name", "id asc");
				foreach ($relprojects as $relproject) {
					$this->set('related_original_username', $relproject['ProjectShare']['related_username']);
					$this->set('related_original_project_id', $relproject['ProjectShare']['related_project_id']);
				}
			}
		
		/**
		* project locking
		**/
		if ($project['Project']['locked'] == 0) {
			$isLocked = false;
		} else {
			$isLocked = true;
		}
		
		/*Generate ribbon thumbnail for featured project
		@ author Ashok Gond
		*/
		$isFeaturedProject = $this->FeaturedProject->hasAny("project_id = $pid");
		if($isFeaturedProject)
		{
			$timestamp = $this->FeaturedProject->field('timestamp',"project_id = $pid");
			
			 $text =$this->convertDate($timestamp);
			 $image_name =$this->ribbonImageName($timestamp);
			 $this->Thumb->generateThumb($ribbon_image='project_ribbon.gif',$text,$dir="large_ribbon",$image_name,$dimension='50x40',125,125);
			 $this->set('image_name',$image_name);
		}//if $isFeaturedProject
		
		//sets the tags relating to this project
		$project_tags = $this->ProjectTag->findAll("project_id = $project_id");
		
		$final_tags = Array();
		$all_tags = Array();
		$counter = 0;
		foreach ($project_tags as $current_tag) {
			$tag_id = $current_tag['ProjectTag']['tag_id'];
			$current_tag_id = $current_tag['Tag']['id'];
			$current_tag = $this->set_tag($project_id, $logged_id, $current_tag);
			
			if (in_array($current_tag_id, $all_tags)) {
			} else {
				array_push($all_tags, $current_tag_id);
				$final_tags[$counter] = $current_tag;
				$counter++;
			}
		}
		
		$user_status = 'normal';
		if ($isLogged) {
			$current_user = $this->User->find("User.id = $logged_id");
			$user_status = $current_user['User']['status'];
		}
		
		
		$isProjectOwner = $owner_id == $logged_id;
		$gallery_count = 0;
		if ($isLogged) {
			$gallery_count = $this->Gallery->findCount("Gallery.user_id = $logged_id");
		}
		
		if ($gallery_count == 0) {
			$isGalleryOwner = false;
		} else {
			$isGalleryOwner = true;
		}
		
		$mpcomments = Array();
		if ($isLogged) {
			$mpcomments = $this->Mpcomment->findAll("user_id = $current_user_id");
		}
		$this->set_admin_name($pid);
		$this->set_admin_time($pid);
		$this->set('isProjectOwner', $isProjectOwner);
		$this->set('isGalleryOwner', $isGalleryOwner);
		$this->set('already_loved', $already_loved);
		$this->set('lovers_count', (int)$project['Project']['loveit']);
		$this->set('project_tags', $final_tags);
		$this->set('user_status', $user_status);
		$this->set('isLocked', $isLocked);
		$this->set('viewcount', $viewcount);
		$this->set('user_projects', $user_projects);
        $this->set('proj_visibility', $project['Project']['proj_visibility']);
        $this->set('gallerylist', $this->getGalleryList($pid));
		$this->set('comments', $final_comments);
		$this->set('mpcomments',$mpcomments);
		$this->set('isLogged', $isLogged);
		$this->set('admin_name', $admin_name);
		$this->set('isCensored', $isCensored);
		$this->set('status', $project['Project']['status']);
		$this->set('flags', $project_flags);
        $this->set('pid', $pid);
        $this->set('owner_id', $project['User']['id']);
        $this->set('urlname', $urlname);
        $this->set('project',$project);
		$this->set('isProjectOwner', $this->activeSession($project['User']['id']));
		$this->set('isMine', $this->activeSession($project['User']['id']));
		$this->set('isFeatured', $this->FeaturedProject->hasAny("project_id = $pid"));
		$this->set('date', friendlyDate($project['Project']['created']));
		$this->set('relatedcount', count($this->ProjectShare->findAll("related_project_id = $pid AND related_project_id != project_id group by project_id, user_id")));
		$this->set('downloadcount', $this->Downloader->findCount(array('project_id' => $pid)));
		$this->set('project_id', $project['Project']['id']);
		$taggers_data = $this->ProjectTag->query("SELECT COUNT(DISTINCT user_id) FROM `project_tags` AS `ProjectTag` WHERE project_id = '".$pid."'");
		$taggers = $taggers_data[0][0]['COUNT(DISTINCT user_id)'];
		$this->set('taggers',$taggers);

		$this->set('urlname', $urlname);

            $this->render('projectcontent','scratchr_projectpage');

		} else if ($urlname) {

			// url = /projects/urlname

			// render user specify projects listing page (i.e. user home page)
			// we could probably bypass these redundant user lookups by
			// using cookie/session info

			/*
			$user_record = $this->User->find("urlname = '$urlname'");
			if (empty($user_record))
				$this->__err();

			$user_id = $user_record['User']['id'];
			$this->modelClass = "Project";
			$criteria = Array("user_id"=>$user_id);
			$options = Array("url"=>"/users/" . $urlname, "show"=>20);
			list($order,$limit,$page) = $this->Pagination->init($criteria, $options);
			$projects = $this->Project->findAll($criteria, NULL, $order, $limit, $page);
			$this->set('projects',$projects);

			$this->set('user', $user_record);
			$this->set('urlname', $urlname);
			$this->set('isMe', $this->activeSession($user_id));
			$this->render('userprojects', 'scratchr_userpage');
			*/

			$this->cakeError('error404');

        } else {

			// url = /projects

			/*
            $this->modelClass = "Project";
            $options = Array("url"=>"/projects");
            $this->Pagination->ajaxAutoDetect = false;
            list($order,$limit,$page) = $this->Pagination->init(null, $options);
            $this->Project->bindUser();
            $data = $this->Project->findAll(NULL, NULL, $order, $limit, $page);
            $this->set('data',$data);
            $this->set('projectlisting', true);
            $this->render('explorer');
			*/

			$this->cakeError('error404');
        }
    }

	
	/**
	* Upload
	**/
    function upload() {
        $this->exitOnInvalidArgCount(0);

        // check referrer for possible urlnames (this kinda stuff should be done through sessions)
        // urlname should be stored as hidden field in the view

        $user_record = $this->Session->read('User');
        if (empty($user_record)) {
			$this->setFlash(___("You need to login to upload", true), FLASH_NOTICE_KEY);
			exit;
        }

		if (!empty($this->params["form"])) {
			$binary_file = $this->params["form"]["binary_file"];
			$mini_thumbnail_file = $this->params["form"]["thumbnail_image"];
			$medium_thumbnail_file = $this->params["form"]["preview_image"];

			// add project owner info
			$user_id = $user_record['id'];
			$urlname = $user_record['urlname'];
			$this->data['Project']['user_id'] = $user_id;

			// get time info
			// note: if we really want to do this correctly
			// taking into account relative time/timezones/daylight savings etc,
			// then this is not sufficient. This only sets
			// the time to the current time at the server machine. You'd
			// probably have to save user timezone info in db or use ipaddress
			// or session var and then do some time manips/conversions when
			// presenting back the time to the view page. Oh, and then you
			// have to take into account daylight savings time...oh the horror!
			// Apparently mysql keeps a session 'time_zone' var. (pr($_SESSION)..
			// no where to be found)
			//
			// $session = $this->Session->read();
			// pr(date('M j, Y \a\t g:iA',$session['Config']['time']));
			// pr(date('M j, Y \a\t g:iA',time()));
			$this->data['Project']['created'] = date("Y-m-d G:i:s");
			$this->data['Project']['name'] = strtolower(trim($this->params['form']['project_name']));
			$this->data['Project']['description'] = trim($this->params['form']['project_description']) ;

			if ($this->Project->save($this->data['Project']))
			{
				$project_id = $this->Project->getLastInsertID();

				$binary_file = (!empty($this->params["form"]["binary_file"])) ? $this->params["form"]["binary_file"]:null;
				$thumbnail_file = (!empty($this->params["form"]["thumbnail_image"])) ? $this->params["form"]["thumbnail_image"]:null;
				$preview_file = (!empty($this->params["form"]["preview_image"])) ? $this->params["form"]["preview_image"]:null;


		        if (isset($binary_file['error']) && !$binary_file['error']) {
					$bin_file = WWW_ROOT . getBinary($urlname, $project_id, false, DS);
					mkdirR(dirname($bin_file) . DS);
					if (!move_uploaded_file($binary_file['tmp_name'], $bin_file)) {
						$this->setFlash(___("Unable to upload scratch file", true), FLASH_ERROR_KEY);
						return;
					}
				}

		       if (isset($thumbnail_file['error']) && !$thumbnail_file['error']) {
					$sm_thumbnail_file = WWW_ROOT . getThumbnailImg($urlname, $project_id, 'mini', false, DS);
					mkdirR(dirname($sm_thumbnail_file) . DS);
					if (!move_uploaded_file($thumbnail_file['tmp_name'], $sm_thumbnail_file)) {
						$this->setFlash(___("Unable to upload mini image", true), FLASH_ERROR_KEY);
						return;
					}
				}

				if (isset($preview_file['error']) && !$preview_file['error']) {
					$med_thumbnail_file = WWW_ROOT . getThumbnailImg($urlname, $project_id, 'medium', false, DS);
					mkdirR(dirname($med_thumbnail_file) . DS);
					if (!move_uploaded_file($preview_file['tmp_name'], $med_thumbnail_file)) {
						$this->setFlash(___("Unable to upload preview image", true), FLASH_ERROR_KEY);
						return;
					}
				}

				// process tags
				if (!empty($this->params['form']['project_tags'])) {
					$tagsarray = explode(",",htmlspecialchars($this->params['form']['project_tags']));
					foreach ($tagsarray as $tag) {
						$ntag = strtolower(trim($tag));
						if (!strcmp($ntag,""))
							continue;
						$this->Tag->bindProjectTag(array("project_id" => $project_id));
						$tag_record = $this->Tag->find("name = '$ntag'",null,null,2);

						if (!empty($tag_record)) {
							if (empty($tag_record['ProjectTag'])) {
								// create project_tag record
								$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id' => $project_id, 'tag_id' =>$tag_record['Tag']['id'], 'user_id' => $user_id)));
								$this->ProjectTag->id=null;
							}
						} else {
							// create tag record
							$this->Tag->save(array('Tag'=>array('name'=>$ntag)));
							$this->Tag->id=null; // otherwise things will be overridden
							$tag_id = $this->Tag->getLastInsertID();
							$this->ProjectTag->save(array('ProjectTag'=>array('id' => null, 'project_id'=>$project_id, 'tag_id'=>$tag_id, 'user_id' => $user_id)));
							$this->ProjectTag->id=null;
						}
					}
				}

				$this->setFlash(___("Project Uploaded", true), FLASH_NOTICE_KEY);
			}
			else
				$this->setFlash(___("Unable to save project info to DB", true), FLASH_ERROR_KEY);
		}
    }
	
	function mods($urlnameignored = null, $pid = null) {
		$this->exitOnInvalidArgCount(2);
		$modpids = $this->ProjectShare->findAll("related_project_id = $pid AND related_project_id != project_id group by project_id, user_id");
		foreach ($modpids as $modpid) {	
			$this->Project->bindUser();
			$this->Project->id = $modpid['ProjectShare']['project_id'];
			$project = $this->Project->read();
			if ($project['User']['username']){
				$mods['linkable'][] = array('username' => $project['User']['username'], 'pid'  => $modpid['ProjectShare']['project_id']); 
			} else {
				$user = $this->User->findById($modpid['ProjectShare']['user_id']);
				if($user['User']['username']) {
					$mods['userlinkable'][] = array('username' => $user['User']['username'], 'pid' => $modpid['ProjectShare']['project_id']);
				} else {
					$mods['unlinkable'][] = array('user_id' => $modpid['ProjectShare']['user_id'], 'pid' => $modpid['ProjectShare']['project_id']);  
				}
			}
			$this->set('mods', $mods);
		}
		$this->render('mods', 'scratchr_default');
		return;
	}	
	
	/**
	/	Change project safe level
	/ 	$safe_level - see database project.safe
	**/
	function set_status($project_id, $safe_level,$urlname=null) {
		$this->checkPermission('project_view_permission');
		$user_id = $this->getLoggedInUserID();
		$this->Project->id = $project_id;
		$this->Project->bindUser();
		$project = $this->Project->read();
		$username = $project['User']['username'];
		$this->Project->saveField("status", $safe_level);
		
		$this->check_project_flag($user_id, $project_id);
		
		$final_flags = $this->ProjectFlag->find("project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		
		if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
			$isCensored = true;
		} else {
			$isCensored = false;
		}
		
		$this->set_project_flag_timestamp($project_id);
		$this->ProjectFlag->set_admin($flag_id, $user_id);
		$this->set_admin_name($project_id, $user_id);
		$this->set_admin_time($project_id);
		$this->set('urlname',$urlname);
		$this->set('isCensored', $isCensored);
		$this->set('status', $safe_level);
		$this->set('flags', $final_flags);
		$this->set('project_id', $project_id);
		$this->set('isFeatured', $this->FeaturedProject->hasAny("project_id = $project_id"));
		$this->render('project_set_attribute_ajax', 'ajax');
	}
	
	/**
	/* Sets the value of an attribute for a project
	**/
	function set_attribute($project_id, $attribute, $state,$urlname=null) {
		$this->autoRender=false;
		$this->checkPermission('project_view_permission');
		$project = $this->Project->find("Project.id = $project_id");
		$user_id = $this->getLoggedInUserID();
		$this->check_project_flag($user_id, $project_id);

		$flags = $this->ProjectFlag->find("project_id = $project_id");
		$this->ProjectFlag->id = $flags['ProjectFlag']['id'];
		$project_flags = $this->ProjectFlag->read();
		$this->ProjectFlag->saveField($attribute, $state);
		
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		$final_flags['ProjectFlag'][$attribute] = $state;
		$flag_id = $final_flags['ProjectFlag']['id'];
		
		if ($project['Project']['proj_visibility'] == 'censbyadmin' || $project['Project']['proj_visibility'] == 'censbycomm') {
			$isCensored = true;
		} else {
			$isCensored = false;
		}
		
		$this->ProjectFlag->set_admin($flag_id, $user_id);
		$this->set_admin_name($project_id, $user_id);
		$this->set_admin_time($project_id);
		$this->set('urlname',$urlname);
		$this->set('isCensored', $isCensored);
		$this->set('status', $project['Project']['status']);
		$this->set('flags', $final_flags);
		$this->set('project_id', $project_id);
		$this->set('isFeatured', $this->FeaturedProject->hasAny("project_id = $project_id"));
		$this->render('project_set_attribute_ajax', 'ajax');
		return;
	}

	/* display long list of galleries that this project belongs to */
	function gallerylist($uname=null, $pid=null) {
		$this->pageTitle = ___("Scratch | Projects | Gallery List", true);
		$this->Project->id = $pid;
		$this->Project->bindUser();
		$project = $this->Project->read();
		if ($project == null || $uname != $project['User']['username']) {
			$this->set('error', true);
			return;
		}
		$this->set('error', false);
		$this->set('proj_name', $project['Project']['name']);		

		$this->modelClass = "GalleryProject";
		$options = array("sortBy"=>"timestamp", "direction"=>"DESC");
		
		$this->Pagination->show = 5;
		$this->modelClass = "GalleryProject";
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "GalleryProject", 
					"direction"=> "DESC", "url"=>"gallerylist/$uname/$pid");
		list($order,$limit,$page) = $this->Pagination->init("project_id = $pid", Array(), $options);

		$this->set('data', $this->getGalleryList($pid, $order, $limit, $page));
		$this->set('option', 'newest');
		$this->render("gallerylist_ajax");
	}
	
	/**
	* List of galleries a project belong's to
	**/
	function getGalleryList($pid=null, $order=null, $limit=null, $page=null) {
		$project = $this->Project->find("Project.id = $pid");
		$creator_id = $project['Project']['user_id'];
		$isLogged = $this->isLoggedIn();
		$gallerylist = array();
		$this->GalleryProject->bindGallery();
		$results = $this->GalleryProject->findAll("project_id = $pid", NULL, $order, $limit, $page);
		foreach ($results as $result) {
			$current_gallery = $result;
			$gallery_id = $result['Gallery']['id'];
			$owner_id = $result['Gallery']['user_id'];
			$temp_gallery = $current_gallery;
			$temp_gallery['Gallery']['ignored'] = false;
			
			if ($isLogged) {
				if (empty($owner_id)) {
				} else {
					$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $owner_id");
				}
				if ($ignore_count > 0) {
					$temp_gallery['Gallery']['ignored'] = true;
				} else {
					$temp_gallery['Gallery']['ignored'] = false;
				}
			}
			array_push($gallerylist, $temp_gallery);
		}
		$gallerylist = $this->finalize_galleries($gallerylist);
		return $gallerylist;
	}
	
	/**
	* Helper function for ajax rendering of comments
	* @project_id=> project identifies
	*/
	function renderComments($project_id, $current_page = null) {
		$current_page = null;
		$this->autoRender = false;
		$this->Project->id=$project_id;
        $project = $this->Project->read();
		$owner_id = $project['User']['id'];
		$user_id = $this->getLoggedInUserID();	
		$isLogged = $this->isLoggedIn();

        if (empty($project)) exit();
		
		$final_comments = $this->set_comments($project_id, $owner_id, $isLogged);
		$this->set_comment_errors(Array());
		
		$this->set('isProjectOwner', $user_id == $project['User']['id']);
		$this->set('isMine', $user_id == $project['User']['id']);
		$this->set('urlname', $project['User']['urlname']);
		$this->set('isLogged', $isLogged);
		$this->set('pid', $project_id);
		$this->set('project_id', $project_id);
		$this->set('comments', $final_comments);
		$this->render('render_comments_ajax', 'ajax');
	}
	
	/**
	* Expands the description of a project in the explorer view
	**/
	function expandDescription($project_id, $secondary = null) {
		$this->autoRender = false;
		$this->Project->id=$project_id;
        $project = $this->Project->read();
		$user_id = $this->getLoggedInUserID();	
		$isLogged = $this->isLoggedIn();
		
		$this->set('project', $project);
		$this->render('expand_description_ajax', 'ajax');
	}
	
	/**
	* Allows owners to lock their projects
	**/
	function lock($pid, $option = null) {
		$this->autoRender = false;
		$this->Project->id=$pid;
        $project = $this->Project->read();
		if ($option == 'lock') {
			$this->Project->saveField('locked', 1);
			$isLocked = true;
		} elseif ($option = 'unlock') {
			$this->Project->saveField('locked', 0);
			$isLocked = false;
		}
		
		
		$this->set('isLocked', $isLocked);
		$this->set('isMine', $this->activeSession($project['User']['id']));
		$this->set('pid', $pid);
		$this->render('project_lock_ajax', 'ajax');
		return;
	}
	
	/**
	* Gets the size of a single set of tags based on $tag_count
	**/
	function getTagSize($tag_count) {
		$size = 1;
		if ($tag_count > 27) {
			$size = 4;
		} elseif ($tag_count > 9) {
			$size = 3;
		} elseif ($tag_count > 1) {
			$size = 2;
		}
		return $size;
	}
	
	/**
	* Returns the tag_sizes based on the array of tags
	**/
	function getTagSizes($tag_array, $project_id) {
		$final_tags = Array();
		$counter = 0;
		foreach ($tag_array as $current_tag) {
			$current_id = $current_tag['Tag']['id'];
			$tag_count = $this->ProjectTag->findCount("project_id = $project_id AND tag_id = $current_id");
			$current_tag['ProjectTag']['size'] = $this->getTagSize($tag_count);

			$final_tags[$counter] = $current_tag;
			$counter++;
		}
		return $final_tags;
	}
	
	/**
	* Helper for displaying comment reply box
	**/
	function show_comment_reply($comment_id, $comment_level) {
		$this->autoRender = false;

		$this->set('comment_id', $comment_id);
		$this->set('comment_level', $comment_level);
		$this->render('show_comment_reply_ajax', 'ajax');
	}
	
	/**
	* Adds a reply to a comment
	**/
	function comment_reply($source_id, $comment_level) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$source_comment = $this->Pcomment->find("Pcomment.id = $source_id");
		$project_id = $source_comment['Pcomment']['project_id'];
		$project = $this->Project->find("Project.id = $project_id");
		$project_name = htmlspecialchars($project['Project']['name']);
		$urlname = $project['User']['urlname'];
		$commenter_id = $this->getLoggedInUserID();
		$project_owner_id = $project['User']['id'];
		$comment_owner_id = $source_comment['User']['id'];
		
		$project_url = "/projects/$urlname/$project_id";
		$project_link = "<a href='$project_url'>$project_name</a>";

        if (!empty($this->params['form'])) {
            if ($user_id) {
				$content_name = 'project_comment_reply_input_' . $source_id;
                $comment = htmlspecialchars($this->params['form'][$content_name]);

				// SPAM checking
				$possible_spam = false;
				$excessive_commenting = false;
				$days = COMMENT_SPAM_MAX_DAYS;
				$max_comments = COMMENT_SPAM_CLEAR_COMMENTS;
				$time_limit = COMMENT_SPAM_CLEAR_MINUTES;
				$recent_comments_by_user = $this->Pcomment->findAll("Pcomment.user_id =  $commenter_id AND Pcomment.created > now() - interval $time_limit minute AND Pcomment.project_id = $project_id");
				if(sizeof($recent_comments_by_user)>$max_comments)
				{
				  $excessive_commenting = false;
				}
				$nowhite_comment = ereg_replace("[ \t\n\r\f\v]{1,}", "[ \\t\\n\\r\\f\\v]*", $comment);
				$similar_comments = $this->Pcomment->findAll("Pcomment.content RLIKE '".$nowhite_comment."' AND Pcomment.created > now() - interval $days  day AND Pcomment.user_id = $commenter_id");
				preg_match_all("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $comment, $url_matches);
				for($i=0; $i<count($url_matches[0]); $i++)
				{
				  $url_to_check = $url_matches[0][$i];
				  if(sizeof($this->Pcomment->findAll("Pcomment.content LIKE '%".$url_to_check."%' AND Pcomment.created > now() - interval $days  day AND Pcomment.user_id = $commenter_id"))>1)
				  {
				      $possible_spam = true;
				  }
				}
				if(sizeof($similar_comments)>1)
				{
				    $possible_spam = true;
				}

				if(isInappropriate($comment)) {
					$vis = 'censbyadmin';
					$this->notify('inappropriate_pcomment_reply', $user_id,
								array('project_id' => $project_id,
								'project_owner_name' => $urlname),
								array($comment));
				} else {
					$vis = 'visible';
				}
				
				$comment_length = strlen($comment);
				if($possible_spam)
				{
				  ___("Sorry, you have posted a very similar message recently.");
				}
				else if($excessive_commenting)
				{
				  ___("Take a break and try posting the comment in a few moments.");
				}
				else if ($comment_length > MAX_COMMENT_LENGTH) {
					
				} else {
					$duplicate_record = $this->Pcomment->find("Pcomment.project_id = $project_id AND Pcomment.user_id = $user_id AND Pcomment.content = '$comment'");

					if (empty($duplicate_record)) {
						$duplicate = false;
					} else {
						$original = $duplicate_record['Pcomment']['timestamp'];
						$today = time(); /* Current unix time */
						$since = $today - strtotime($original);
						if ($since < 30) {
							$duplicate = true;
						} else {
							$duplicate = false;
						}
					}
					
					if ($duplicate) {
					} else {
						$new_reply = array('Pcomment'=>array('id' => null, 'project_id'=>$project_id, 'user_id'=>$user_id, 'content'=>$comment, 'comment_visibility'=>$vis, 'reply_to' => $source_id));
						$this->Pcomment->save($new_reply);
					
						$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.user_id = $commenter_id AND (IgnoredUser.blocker_id = $project_owner_id OR IgnoredUser.blocker_id = $comment_owner_id)");
						if ($ignore_count == 0 && $vis == 'visible') {
							//user is not replying to his own comment
							if($user_id != $comment_owner_id) {
								//comment reply notification to comment_owner
								$this->notify('new_pcomment_reply', $comment_owner_id,
										array('project_id' => $project_id,
										'project_owner_name' => $urlname,
										'from_user_name' => $this->getLoggedInUsername())
									);
							}
							//send notification to project owner if project owner and comment owner are differnt
							//and comment replier is not the project owner
							if($project_owner_id != $comment_owner_id && $user_id != $project_owner_id) {
								$this->notify('new_pcomment', $project_owner_id,
											array('project_id' => $project_id,
											'from_user_name' => $this->getLoggedInUsername())
										);
							}
						}
					}
				}
			}
		}
		
		
		$final_comments = $this->set_replies($project_id, $source_id, $user_id);

		$this->set('project_id', $project_id);
		$this->set('isProjectOwner', $user_id == $project['User']['id']);
		$this->set('comments', $final_comments);
		$this->set('comment_level', $comment_level + 1);
		$this->set('isLogged', $isLogged);
		$this->render('comment_reply_ajax', 'ajax');
	}

	/**
	* Displays the list of replies for a comment
	**/
	function display_replies($source_id, $comment_level) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$source_comment = $this->Pcomment->find("Pcomment.id = $source_id");
		$project_id = $source_comment['Pcomment']['project_id'];
		$project = $this->Project->find("Project.id = $project_id");
		
		$final_comments = $this->set_replies($project_id, $source_id, $user_id);
		
		$this->set('isProjectOwner', $user_id == $project['User']['id']);
		$this->set('project_id', $project_id);
		$this->set('comments', $final_comments);
		$this->set('comment_level', $comment_level + 1);
		$this->set('isLogged', $isLogged);
		$this->render('comment_reply_ajax', 'ajax');
	}
	
	/**
	* Comment Delete
	**/
	function delete_comment($project_id, $comment_id) {
		$this->autoRender=false;
		$this->Project->bindUser();
        $this->Project->id=$project_id;
        $project = $this->Project->find("Project.id = $project_id");
		$user_id = $this->getLoggedInUserID();
		
		$comment = $this->Pcomment->find("Pcomment.id = $comment_id");
		$comment_owner_id = $comment['User']['id'];
		$project_owner_id = $project['User']['id'];
		$isProjectOwner = $project_owner_id == $user_id;
		$isCommentOwner = $comment_owner_id == $user_id;

		if (empty($project)) $this->cakeError('error404');

		//if the user is not an admin, or not the project owner or not the comment owner, check if s/he has special permission
		if (!($this->isAdmin() || $isProjectOwner || $isCommentOwner)) {
			$this->checkPermission('delete_project_comments');
		}
		
		$this->Pcomment->del($comment_id);
		exit;
	}
	
	/**
	* Returns all comments relevant to logged in user viewing a project
	**/
	function set_comments($project_id, $creator_id, $isLogged) {
		$user_id = $this->getLoggedInUserID();
		
		$this->PaginationSecondary->show = 60;
		$this->modelClass = "Pcomment";
		$options = Array("sortBy"=>"created", "sortByClass" => "Pcomment", 
					"direction"=> "DESC", "url"=>"renderComments/$project_id/0");
		list($order,$limit,$page) = $this->PaginationSecondary->init("project_id = $project_id AND Pcomment.comment_visibility = 'visible' AND reply_to = -100", Array(), $options);
		$comments = $this->Pcomment->findAll("project_id = $project_id AND Pcomment.comment_visibility = 'visible' AND reply_to = -100", null, $order, $limit, $page);
	
		//set comments info
		$counter = 0;
		$final_comments = Array();
		foreach ($comments as $current_comment) {
			$temp_comment = $current_comment;
			$current_id = $temp_comment['Pcomment']['id'];
			$commenter_id = $temp_comment['Pcomment']['user_id'];
			$temp_comment['Pcomment']['ignored'] = false;
			$temp_comment['Pcomment']['commented'] = false;
			
			$comment_content = $current_comment['Pcomment']['content'];
			$comment_content = $this->set_comment_content($comment_content);
			$temp_comment['Pcomment']['content'] = $comment_content;
			
			$reply_count = $this->Pcomment->findCount("project_id = $project_id AND reply_to = $current_id");
			$temp_comment['Pcomment']['replies'] = $reply_count;
			
			if ($isLogged) {
				$mp_count = $this->Mpcomment->findCount("user_id = $user_id AND comment_id = $current_id");
				if ($mp_count > 0) {
					$temp_comment['Pcomment']['commented'] = true;
				}
				
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $commenter_id");
				$ignore_count = $ignore_count + $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $commenter_id");
				if ($ignore_count > 0) {
					$temp_comment['Pcomment']['ignored'] = true;
				} else {
					$temp_comment['Pcomment']['ignored'] = false;
				}
			} else {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $commenter_id");
				if ($ignore_count > 0) {
					$temp_comment['Pcomment']['ignored'] = true;
				} else {
					$temp_comment['Pcomment']['ignored'] = false;
				}
			}
			$all_replies = $this->set_replies($project_id, $current_id, $user_id, 3);
			$temp_comment['Pcomment']['replylist'] = $all_replies;
			$final_comments[$counter] = $temp_comment;
			$counter++;
		}
		return $final_comments;
	}
	
	function set_comment_errors($errors = Array()) {
		if (empty($errors)) {
			$this->set('isCommentError', false);
			$this->set('commentErrors', $errors);
		} else {
			$this->set('isCommentError', true);
			$this->set('commentErrors', $errors);
		}
	}
	/**
	* Returns all replies relevant to logged in user viewing a project
	**/
	function set_replies($project_id, $source_id, $user_id, $limit = 0) {
		$project = $this->Project->find("Project.id = $project_id");
		$creator_id = $project['Project']['user_id'];
		
		$all_replies = $this->Pcomment->findAll("project_id = $project_id AND comment_visibility = 'visible' AND reply_to = $source_id");
		$isLogged = $this->isLoggedIn();
		
		$counter = 0;
		$final_comments = Array();
		foreach ($all_replies as $current_comment) {
			$temp_comment = $current_comment;
			$current_id = $temp_comment['Pcomment']['id'];
			$temp_comment['Pcomment']['commented'] = false;
			$temp_comment['Pcomment']['ignored'] = false;
			$commenter_id = $temp_comment['Pcomment']['user_id'];
			
			$comment_content = $current_comment['Pcomment']['content'];
			$comment_content = $this->set_comment_content($comment_content);
			$temp_comment['Pcomment']['content'] = $comment_content;
			
			$reply_count = $this->Pcomment->findCount("project_id = $project_id AND reply_to = $current_id");
			$temp_comment['Pcomment']['replies'] = $reply_count;
			if ($isLogged) {
				$mp_count = $this->Mpcomment->findCount("user_id = $user_id AND comment_id = $current_id");
				if ($mp_count > 0) {
					$temp_comment['Pcomment']['commented'] = true;
				}
				
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $commenter_id");
				$ignore_count = $ignore_count + $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $commenter_id");
				if ($ignore_count > 0) {
					$temp_comment['Pcomment']['ignored'] = true;
				} else {
					$temp_comment['Pcomment']['ignored'] = false;
				}
			} else {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $commenter_id");
				if ($ignore_count > 0) {
					$temp_comment['Pcomment']['ignored'] = true;
				} else {
					$temp_comment['Pcomment']['ignored'] = false;
				}
			}
			$final_comments[$counter] = $temp_comment;
			$counter++;
		}
		
		if ($limit == 0) {
		} else {
			$length = count($final_comments);
			if ($limit >= $length) {
			} else {
				$final_comments = array_splice($final_comments, 0, $limit);
			}
		}
		return $final_comments;
	}
	
	/**
	* Helper for setting up html links for comments
	**/
	function set_comment_content($initial_content) {
		$comment_content = $initial_content;
		$comment_content  = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/projects/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to project', true) . ")</a>",  $comment_content);
		$comment_content  = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/forums/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to forums', true) . ")</a>",  $comment_content);
		$comment_content = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/galleries/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to gallery', true) . ")</a>",  $comment_content);
		
		return $comment_content;
	}

	/**
	* Helper for checking whether a project has a corresponding entry in the projects_flag table 
	**/
	function check_project_flag($user_id, $project_id) {
		if ($this->ProjectFlag->findCount("ProjectFlag.project_id = $project_id") == 0) {
			$info = Array('ProjectFlag' => Array('id' => null, 'user_id' => $user_id, 'project_id' => $project_id));
			$this->ProjectFlag->save($info);
		}
		
		return;
	}
	
	/**
	* Helper for finding the admin name
	**/
	function get_admin_name($project_id, $user_id = "") {
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		$admin_id = $final_flags['ProjectFlag']['admin_id'];
		if ($admin_id == 0) {
			$admin_name = "None";
		} else {
			$admin = $this->User->find("User.id = $admin_id");
			$admin_name = $admin['User']['username'];
		}
		
		if ($user_id == "") {
			return $admin_name;
		} else {
			$admin = $this->User->find("User.id = $user_id");
			$admin_name = $admin['User']['username'];
			return $admin_name;
		}
	}
	
	/**
	* Helper for setting the admin name
	**/
	function set_admin_name($project_id, $user_id = "") {
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		$admin_id = $final_flags['ProjectFlag']['admin_id'];
		$feature_admin_id = $final_flags['ProjectFlag']['feature_admin_id'];
		
		
		if ($admin_id == 0) {
			$admin_name = "None";
		} else {
			$admin = $this->User->find("User.id = $admin_id");
			$admin_name = $admin['User']['username'];
		}
		
		if ($feature_admin_id == 0) {
			$feature_admin_name = "None";
		} else {
			$feature_admin = $this->User->find("User.id = $feature_admin_id");
			$feature_admin_name = $feature_admin['User']['username'];
		}
		
		if ($user_id == "") {
		} else {
			$feature_admin = $this->User->find("User.id = $user_id");
			$feature_admin_name = $feature_admin['User']['username'];
		}
		
		$this->set('feature_admin_name', $feature_admin_name);
		$this->set('admin_name', $admin_name);
	}
	
	function set_admin_time($project_id, $overload_time = "") {
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		
		$unformatted_time = $final_flags['ProjectFlag']['timestamp'];
		$admin_id = $final_flags['ProjectFlag']['admin_id'];
		$feature_admin_id = $final_flags['ProjectFlag']['feature_admin_id'];
		$actual_time = stampToDate($unformatted_time);
		if ($admin_id == 0) {
			$actual_time = "";
		}
		
		if ($feature_admin_id == 0) {
			$feature_time = "";$feature_on = "";
		} else {
			$feature_timestamp = $final_flags['ProjectFlag']['feature_timestamp'];
			$feature_time = stampToDate($feature_timestamp);
			$feature_on = $feature_timestamp;
		}
		
		if ($overload_time == "") {
		} else {
			$feature_time = stampToDate($overload_time);
			$feature_on = $overload_time;
		}
		$this->set('feature_on', $feature_on);
		$this->set('feature_time', $feature_time);
		$this->set('admin_time', $actual_time);
	}
	
	function set_project_flag_timestamp($project_id, $mode = "normal", $overload_time = "") {
		$final_flags = $this->ProjectFlag->find("ProjectFlag.project_id = $project_id");
		$flag_id = $final_flags['ProjectFlag']['id'];
		$admin_id = $final_flags['ProjectFlag']['admin_id'];
		$this->ProjectFlag->set_admin($flag_id, $admin_id);
		
		if ($mode == "featured") {
			$this->ProjectFlag->set_admin_time($flag_id, $overload_time);
		}

	}
}
?>