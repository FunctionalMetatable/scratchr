<?php
Class GalleriesController extends AppController {

	var $name = "Gallery";
    var $uses = array("ClubbedGallery", "IgnoredUser", "GalleryFlag", "Mgcomment", "Tag", "GalleryTag", "TagFlag", "Project", "ClubbedGallery", "FeaturedGallery", "GalleryProject", "Gallery", "GalleryMembership","Gcomment","User","RelationshipType","Relationship","GalleryRequest", "Relationship", "Notification"); //apparently order matters for associative finds
    var $helpers = array('PaginationSecondary', 'Pagination','Ajax','Javascript');
    var $components = array('PaginationSecondary', 'Email',  'Pagination', 'RequestHandler', 'FileUploader');

	/**
	/* Function List
	/* index()
	/* name($gallery_id)
	/* describe($gallery_id)
	/* feature()
	/* defeature()
	/* club()
	/* create($gallery_name)
	/* delete($gallery_id)
	/* comment($gallery_id)
	/* delcomment($gallery_id, $comment_id)
	/* updatepic($gallery_id)
	/* browse($option = null)
	/*
	**/

	/**
     * Renders theme controller error page
	 * Overrides AppController::__err()
     */
    function __err() {
        $this->render('terror');
        die;
    }

	 /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
		$status = $this->getContentStatus();
    }
	//function to set galleries project count.
	function update_total_project(){
		set_time_limit(120);
		//echo $time =ini_get('max_execution_time'); 
		$galleries = $this->Gallery->findAll(null,'id,user_id','id');
		foreach($galleries as $gallery){
		//listing ignored user by gallery owner.
		$gallery_id =$gallery['Gallery']['id'];
		$owner_id =$gallery['Gallery']['user_id'];
		$ignored_user_array =array();
		$ignore_user_list = $this->IgnoredUser->findAll("IgnoredUser.blocker_id = $owner_id",'user_id');
		foreach($ignore_user_list as $ignore_user)
		array_push($ignored_user_array,$ignore_user['IgnoredUser']['user_id']);
		$ignored_list = implode(',',$ignored_user_array);
		//echo $owner_id.':'.$ignored_list.'<BR>';
		
		if(!empty($ignored_list))
		$conditions = "gallery_id = $gallery_id AND Project.user_id not in (".$ignored_list.") ";
		else
		$conditions = "gallery_id = $gallery_id ";
		$projects_count = $this->GalleryProject->findCount($conditions);
		$this->Gallery->id =$gallery_id;
		$this->Gallery->saveField('total_projects',$projects_count);
		
		//echo $gallery_id.':'.$projects_count.'<BR>';
		}//$galleries
		echo "project count has been updated"; die;
	}//function

	/**
	/* GalleryController Index
	/* REDIRECT: ('/galleries/browse/newest')
	**/
	function index() {
		$this->pageTitle = ___("Scratch | Galleries", true);
		$this->modelClass = "Gallery";
		$options = Array("url"=>"/galleries");
		list($order,$limit,$page) = $this->Pagination->init("total_projects > 0", Array(), $options);
		$this->Pagination->show = 10;
		$data = $this->Gallery->findAll(null, NULL, "Gallery.changed ASC", 20, $page); // display only galleries which have atleast 1 project
		$this->set('data',$data);
		$option = "newest";

		$this->redirect('/galleries/browse/newest');
	}


    /**
     * Ajax-updates the title for the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function name($gallery_id) {
	    $this->exitOnInvalidArgCount(1);
		if (!$this->RequestHandler->isAjax())
			exit;

		$session_uid = $this->getLoggedInUserID();
		if (!$session_uid)
			exit;

        $this->autoRender=false;

        $this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();

		if (empty($gallery))
			exit;

		if (!$this->isAdmin())
			if ($gallery['User']['id'] !== $session_uid)
				exit;

		$inputText = (isset($this->params['form']['name'])) ? $this->params['form']['name'] : null;
		if ( $inputText ) {
			$newtitle = htmlspecialchars( $inputText );
			if(isInappropriate($newtitle))
			{
			    $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$this->notify('inappropriate_gtitle', $user_id,
								array('gallery_id' => $gallery_id));
			}
			else
			{
				if ($this->Gallery->saveField('name',$newtitle)) {
				   $this->set('gtitle', $newtitle);
				   $this->render('themetitle_ajax', 'ajax');
				   return;
				}
			}
		}
        $this->set('gtitle', $gallery['Gallery']['name']);
        $this->render('themetitle_ajax','ajax');
        return;
    }


	/**
     * Update description action the given project
     * @param string $urlname => user url
     * @parm int $pid => project id
     */
    function describe($gallery_id) {
        $this->exitOnInvalidArgCount(1);
		if (!$this->RequestHandler->isAjax())
			exit;

		$session_uid = $this->getLoggedInUserID();
		if (!$session_uid)
			exit;

        $this->autoRender=false;

        $this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();

		if (empty($gallery))
			exit;

		if (!$this->isAdmin())
			if ($gallery['User']['id'] !== $session_uid)
				exit;

        if (isset($this->params['form']['description'])) {
            $newdesc = htmlspecialchars(trim($this->params['form']['description']));
            if(isInappropriate($newdesc))
            {
                $user_record = $this->Session->read('User');
                $user_id = $user_record['id'];
                $this->notify('inappropriate_gdesc', $user_id,
                            array('gallery_id' => $gallery_id));
            }
            else
            {
                if ($this->Gallery->saveField('description', $newdesc)) {
                   $this->set('gdesc', $newdesc);
                   $this->render('themedescription_ajax','ajax');
                   return;
                }
            }
        }
        $this->set('gdesc',$gallery['Gallery']['description']);
        $this->render('themedescription_ajax','ajax');
        return;
    }


	/**
	* Checks to see if the project owner has locked the project
	**/
	function check_locked($user_id) {
		$this->autoRender=false;
        $this->User->id=$user_id;
		$user = $this->User->read();
		$isLocked = $user['User']['status'];

		if ($isLocked == "locked" || $isLocked == "deleted") {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Admin Feature a theme
	 */
	function feature() {
		$this->checkPermission('feature_galleries');
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || empty($this->params['form']))
			exit;
		$gallery_id = $this->params['form']['theme-id'];
		$this->Gallery->set_status($gallery_id, "safe");
		$this->FeaturedGallery->id=null;

		if ($this->FeaturedGallery->save(Array('FeaturedGallery'=>Array('gallery_id'=>$gallery_id))))
		{
			$this->set('theme_id', $gallery_id);
			$this->set("isFeatured", true);
			$this->set("isClubbed", $this->params['form']['isClubbed']);
			$this->render('admin_actions', 'ajax');
		}
	}


	/**
	 * Admin deFeature a theme
	 */
	function defeature() {
		$this->checkPermission('feature_galleries');
		$user_id = $this->getLoggedInUserID();
		if (!$user_id ||  empty($this->params['form']))
			exit;

		$gallery_id = $this->params['form']['theme-id'];
		$featured_gallery = $this->FeaturedGallery->find("gallery_id = $gallery_id");
		
		if (!($featured_gallery == null))
		{
			if ($this->FeaturedGallery->del($featured_gallery['FeaturedGallery']['id']))
			{
				$this->set('theme_id', $gallery_id);
				$this->set("isFeatured", false);
				$this->set("isClubbed", $this->params['form']['isClubbed']);

				$this->render('admin_actions', 'ajax');
			} else {
				$this->render('admin_actions', 'ajax');
			}
		}
	}


	/**
	/* Add a project to the set of clubbed projects
	/* RENDER: "admin_actions"
	/* AJAX
	**/
	function club()
	{
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || !$this->isAdmin() || empty($this->params['form']))
			exit;

		$gallery_id = $this->params['form']['theme-id'];
		$this->ClubbedGallery->id=null;

		if ($this->ClubbedGallery->save(Array('ClubbedGallery'=>Array('gallery_id'=>$gallery_id))))
		{
			$this->set('theme_id', $gallery_id);
			$this->set("isClubbed", true);
			$this->set("isFeatured", $this->params['form']['isFeatured']);
			$this->render('admin_actions', 'ajax');
		}
		exit;
	}


/**
	/* Removes a project from the set of clubbed projects
	/* RENDER: "admin_actions"
	/* AJAX
	**/
	function declub()
	{
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || !$this->isAdmin() || empty($this->params['form']))
			exit;

		$gallery_id = $this->params['form']['theme-id'];
		$clubbed_gallery = $this->ClubbedGallery->find("gallery_id = $gallery_id", NULL, "ClubbedGallery.id DESC");

		if (!empty($clubbed_gallery))
		{
			if ($this->ClubbedGallery->del($clubbed_gallery["ClubbedGallery"]["id"]))
			{
				$this->set('theme_id', $gallery_id);
				$this->set("isClubbed", false);
				$this->set("isFeatured", $this->params['form']['isFeatured']);
				$this->render('admin_actions', 'ajax');
			}
		}
		exit;
	}

	function create() {
		$this->autoRender = false;
		$error_codes = Array();

		//init user variables
		$isLoggedIn = $this->isLoggedIn();
		
		if($isLoggedIn)
		{
		  $this->set('default_name', "");
		  $this->set('default_description', "");
		  $this->set('isCreated', false);
		  $this->set('errors', $error_codes);
		  $this->render('create');
		}
		else
		{
		  $this->redirect('/');
		}
	}

	/**
	 * Creates a new gallery
	 * @param: $themename => new gallery name
	 */
	function handle_create() {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$gallery_id = 0;
		$gallery_name = "none";
		$isCreated = false;
		$error_codes = Array();
		$name = "";
		$description = "";
		
		if (!empty($this->params["form"]["create_gallery_name"]) && !empty($this->params["form"]["create_gallery_description"])) {
			$name = $this->params["form"]["create_gallery_name"];
			$name = str_replace("'", "", $name);
			$gallery_record = $this->Gallery->find("name = '$name'");
			$permission = $this->params["form"]["create_gallery_permission"];

			/**
			* SELF: 1
			* EVERYONE: 0
			* FRIENDS: 3
			**/
			$internal_permission = 1;
			if ($permission == "Self") {
				$internal_permission = 1;
				$usage = "private";
			} elseif ($permission == "Everyone") {
				$internal_permission = 0;
				$usage = "public";
			} elseif ($permission == "Friends") {
				$internal_permission = 3;
				$usage = "friends";
			}

			if(isInappropriate($name))
			{
			    $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$current_error = "name_inappropriate";
				array_push($error_codes, $current_error);
			}

			$description = $this->params["form"]["create_gallery_description"];
			if(isInappropriate($description))
			{
			    $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$current_error = "description_inappropriate";
				array_push($error_codes, $current_error);
			}
			$visibility = 1;
			#intval($this->params["form"]["visibility"]);
			// TODO: assert 'visibility' is within range, 1-3

			if (empty($gallery_record) && empty($error_codes)) {
				if ($this->Gallery->save(Array("Gallery"=>Array("id" => null, "name"=>$name, "description"=>$description, "user_id"=>$user_id, "type"=>$internal_permission, "usage" => $usage)))) {
					$gallery_id = $this->Gallery->getLastInsertID();
					$gallery_name = $name;
					$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'type' => 0, 'rank' => 'owner'));
					$this->GalleryMembership->save($info);
					
					//if gallery is for friends
					if($internal_permission == 3){
						$this->Relationship->bindFriend();
						$relations = $this->Relationship->findAll("Relationship.user_id = $user_id");
			
						foreach($relations as $relation){
							$member = Array('GalleryMembership' => Array('id' => null, 'user_id' => $relation['Relationship']['friend_id'], 'gallery_id' => $gallery_id, 'type' => 3, 'rank' => 'member'));
							$this->GalleryMembership->save($member);
							$this->GalleryMembership->id = false;
						}
					}
					
					$this->set_creation_tags($gallery_id, $this->data);

					if (!empty($this->params["form"]["create_gallery_icon"]["name"])) {
						$icon_array = $this->params["form"]["create_gallery_icon"];
						$gallery_icon_file_orig = "static".DS."icons".DS."gallery".DS."tmp".DS;
						if(!file_exists($gallery_icon_file_orig))
							mkdir($gallery_icon_file_orig);

						$error = $this->FileUploader->handleFileUpload($icon_array, $gallery_icon_file_orig, true);
						if ($error[0])
						{
							$current_error = "image_size";
							array_push($error_codes, $current_error);
							$this->setFlash("$name" . ___(" has been created, however the image file was too large to upload", true), FLASH_NOTICE_KEY);
							//$this->redirect('/galleries/view/'. $gallery_id);
						}
						else
						{
							$gallery_icon_file_orig.=$error[1];
							$this->resizeImage($gallery_icon_file_orig, $gallery_id, false, 'gallery');
							//$this->deleteMovedFile($gallery_icon_file_orig);
						}
					}
					$isCreated = true;
				}
			} else {
				if (!empty($gallery_record)) {
					$current_error = "name_taken";
					array_push($error_codes, $current_error);
				}
			}	
		} else {
			if (empty($this->params["form"]["create_gallery_name"])) {
				$current_error = "empty_name";
				array_push($error_codes, $current_error);
			}
			if (empty($this->params["form"]["create_gallery_description"])) {
				$current_error = "empty_description";
				array_push($error_codes, $current_error);
			}
		}
		
		$this->set('default_name', $name);
		$this->set('default_description', $description);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery_name', $gallery_name);
		$this->set('isCreated', $isCreated);
		$this->set('errors', $error_codes);
		
		if (empty($error_codes)) {
			$this->redirect('/galleries/view/' . $gallery_id);
		} else {
			$this->render('create');
		}
	}
	
	/**
	* Handles the creation of predefined gallery tags at gallery creationb
	**/
	function set_creation_tags($gallery_id, $tag_data) {
		$user_id = $this->getLoggedInUserId();
		foreach($tag_data['Gallery'] as $tag_id => $exists) {
			$admin_tag = $this->AdminTag->find("AdminTag.id = $tag_id");
			if ($exists == 1) {
				$actual_tag_id = $admin_tag['AdminTag']['tag_id'];
				$gallery_tag_info = Array('GalleryTag' => Array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'tag_id' => $actual_tag_id));
				$this->GalleryTag->save($gallery_tag_info);
			}
		}
		return;
	}
	
	/**
	 * Deletes the gallery referred to by gallery_id
	 * @param int $gallery_id => gallery identifier
	 * REDIRECT: /galleries
	 */
	function delete($gallery_id) {
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');
			
		$gallery = $this->Gallery->find("Gallery.id = $gallery_id");
		if (!$gallery)
			$this->cakeError('error404');

		if ($this->isAdmin() || $gallery['Gallery']['user_id'] == $user_id) {
			if ($this->isAdmin()) {
				$this->hide_gallery($gallery_id, "delbyadmin");
			} else {
				$this->hide_gallery($gallery_id, "delbyusr");
			}
			$this->redirect('/users/showgalleries/' . $user_id);
		} else {
			$this->setFlash(___("You are not the owner of this gallery", true), FLASH_NOTICE_KEY);
			$this->redirect('/galleries');
		}
	}

	/**
	 * Adds a comment to a gallery discussion list
	 * @params int $gallery_id => gallery id to add comment
	 */
	function comment($gallery_id) {
	    $this->exitOnInvalidArgCount(1);
        $this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();
		$gallery_owner_id = $gallery['User']['id'];
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$this->PaginationSecondary->show = 60;
		$errors = Array();

		$isLocked = $this->check_locked($user_id);

		if ($isLocked) exit();

        if (empty($gallery))
            exit();

		$commenter_id = null;

        if ($this->activeSession())
        if (!empty($this->params['form'])) {
            $commenter_id = $this->Session->read('User.id');
            if ($commenter_id) {
                $comment = htmlspecialchars($this->params['form']['tcomment_textarea']);

				// SPAM checking
				$possible_spam = false;
				$excessive_commenting = false;
				$days = COMMENT_SPAM_MAX_DAYS;
				$max_comments = COMMENT_SPAM_CLEAR_COMMENTS;
				$time_limit = COMMENT_SPAM_CLEAR_MINUTES;
				$recent_comments_by_user = $this->Gcomment->findAll("Gcomment.user_id =  $commenter_id AND Gcomment.timestamp > now() - interval $time_limit minute AND Gcomment.gallery_id = $gallery_id");
				if(sizeof($recent_comments_by_user)>$max_comments)
				{
				   $excessive_commenting = true;
				}
				$nowhite_comment = ereg_replace("[ \t\n\r\f\v]{1,}", "[ \\t\\n\\r\\f\\v]*", $comment);
				$similar_comments = $this->Gcomment->findAll("Gcomment.content RLIKE '".$nowhite_comment."' AND Gcomment.timestamp > now() - interval $days  day AND Gcomment.user_id = $commenter_id");
				preg_match_all("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $comment, $url_matches);
				for($i=0; $i<count($url_matches[0]); $i++)
				{
				  $url_to_check = $url_matches[0][$i];
				  if(sizeof($this->Gcomment->findAll("Gcomment.content LIKE '%".$url_to_check."%' AND Gcomment.timestamp > now() - interval $days  day AND Gcomment.user_id = $commenter_id"))>1)
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
					$this->notify('inappropriate_gcomment', $commenter_id,
								array('gallery_id' => $gallery_id),
								array($comment)
							);
				} else {
					$vis = 'visible';
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
					$duplicate_record = $this->Gcomment->find("Gcomment.gallery_id = $gallery_id AND Gcomment.user_id = $commenter_id AND Gcomment.content = '$comment'");
					
					if (empty($duplicate_record)) {
						$duplicate = false;
					} else {
						$original = $duplicate_record['Gcomment']['timestamp'];
						$today = time(); /* Current unix time */
						$since = $today - strtotime($original);
						if ($since < 60) {
							$duplicate = true;
						} else {
							$duplicate = false;
						}
					}
					
					if ($duplicate) {
					} else {
						$new_tcomment = array('Gcomment'=>array('id' => null, 'gallery_id'=>$gallery_id, 'user_id'=>$commenter_id, 'content'=>$comment, 'comment_visibility'=>$vis));
						$this->Gcomment->id=null;
						$this->Gcomment->save($new_tcomment);
                        $new_tcomment['Gcomment']['id'] = $this->Gcomment->getInsertID();
                        $this->Gcomment->deleteCommentsFromMemcache($gallery_id);
						$this->updateGallery($gallery_id);
					}
				}
            }
        } else {
			array_push($errors, "Please enter a valid comment.");
		}
		$this->Gcomment->bindUser();
		$commenter_userrecord = $this->User->find("id = $commenter_id");
		$commenter_username = $commenter_userrecord['User']['username'];
		$user_id = $gallery['Gallery']['user_id'];
		$user_record = $this->User->find("id = $user_id");
		$notify_gcomment = $user_record['User']['notify_gcomment'];
		$commenter_id = $this->getLoggedInUserID();
	
		if($notify_gcomment && $vis=='visible' && $gallery['Gallery']['user_id']!=$commenter_id)
		{
			$guser_id = $gallery['Gallery']['user_id'];
			$user_record = $this->User->find("id = $guser_id");
			$username = $user_record['User']['username'];
			$gallery_title = $gallery['Gallery']['name'];
			
			$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.user_id = $commenter_id AND IgnoredUser.blocker_id = $gallery_owner_id");
			if ($ignore_count == 0) {
				//do not notify if gallery owner is the commenter
				if(!empty($new_tcomment['Gcomment']['id'])
                    && $guser_id != $commenter_id) {
					$this->notify('new_gcomment', $guser_id,
								array('gallery_id' => $gallery_id,
								'from_user_name' => $commenter_username,
                                'comment_id' => $new_tcomment['Gcomment']['id']
                                )
							);
				}
			}
		}
		
		$this->set_comment_errors($errors);
		//the comment is saved
        if(!empty($new_tcomment)) {
          $new_tcomment['User'] =  $commenter_userrecord['User'];
          $this->set('comment', $new_tcomment);
          $this->set('isThemeOwner', $user_id == $user_id);
          $this->set('gallery_id', $gallery_id);
        }
		$this->render('themecomments_ajax', 'ajax');
		return;
	}

	/**
	 * Deletes a comment in the target gallery_id
	 * $gallery_id => gallery identifier
	 * $comment_id => comment identifier
	 * REDIRECT: /galleries
	 */
	function delcomment($gallery_id=null, $comment_id=null)
	{
		$this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
		$user_id = $this->getLoggedInUserID();
        $this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$comment = $this->Gcomment->find("Gcomment.id = $comment_id");
		$comment_owner_id = $comment['User']['id'];
		$gallery_owner_id = $gallery['User']['id'];
		$isGalleryOwner = $gallery_owner_id == $user_id;
		$isCommentOwner = $comment_owner_id == $user_id;
		
		if (empty($gallery))
			$this->cakeError('error404');

		if (!($this->isAdmin() || $isGalleryOwner || $isCommentOwner))
			$this->cakeError('error404');

		$this->Gcomment->id = $comment_id;
		$this->Gcomment->saveField("visibility", null) ;

		$final_comments = $this->set_comments($gallery_id, $gallery_owner_id, $user_id, $isLogged);
		$this->set_comment_errors(Array());
		
		$this->set('isLogged', $isLogged);
		$this->set('page', $page);
		$this->set('theme_id', $gallery_id);
		$this->set('isThemeOwner', $user_id == $gallery['User']['id']);
		$this->set('theme_comments',$final_comments);
        $this->render('themecomments_ajax', 'ajax');
	}

	/**
	* Deletes a comment from the gallery
	**/
	function delete_comment($gallery_id, $comment_id) {
		$this->autoRender=false;
		$this->Gallery->bindUser();
		$this->Gallery->id=$gallery_id;
		$gallery = $this->Gallery->read();
		$user_id = $this->getLoggedInUserID();
		
		$comment = $this->Gcomment->find("Gcomment.id = $comment_id");
		$comment_owner_id = $comment['User']['id'];
		$gallery_owner_id = $gallery['User']['id'];
		$isGalleryOwner = $gallery_owner_id == $user_id;
		$isCommentOwner = $comment_owner_id == $user_id;

		if (empty($gallery)) $this->cakeError('error404');

		//if the user is not an admin, or not the gallery owner or not the comment owner, check if s/he has special permission
		if (!($this->isAdmin() || $isGalleryOwner || $isCommentOwner)) {
			$this->checkPermission('delete_gallery_comments');
		}
		
		if($isGalleryOwner || $isCommentOwner) {
			$this->hide_gcomment($comment_id, "delbyusr");
		} else {
			$this->hide_gcomment($comment_id, "delbyadmin");
		}
		$commentLists = $this->Gcomment->findAll('Gcomment.gallery_id = '
                    . $gallery_id . ' AND Gcomment.reply_to = '. $comment_id,'id');
			
			foreach($commentLists as $commentList){
				$this->Gcomment->id = $commentList['Gcomment']['id'];
				$this->Gcomment->saveField('comment_visibility','delbyparentcomment');
				$this->Gcomment->id = false;
			}
        $this->Gcomment->deleteCommentsFromMemcache($gallery_id);
		exit;
	}

	/**
	 * Updates the picture of the specified gallery
	 * $gallery_id => gallery identifier
	 * REDIRECT: /galleries/view
	 */
	function updatepic($gallery_id) {
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (empty($this->params["form"]))
			$this->cakeError('error404');

		$this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();

		if ($gallery == null)
			$this->cakeError('error404');

		if (!$this->isAdmin())
			if ($gallery['User']['id'] !== $user_id)
				$this->cakeError('error404');

		$icon_array = $this->params["form"]["gallery_icon"];

		$gallery_icon_file_orig = "static".DS."icons".DS."gallery".DS."tmp".DS;
		if(!file_exists($gallery_icon_file_orig))
			mkdir($gallery_icon_file_orig);

		$error = $this->FileUploader->handleFileUpload($icon_array, $gallery_icon_file_orig, true);
		if ($error[0])
		{
			$this->Session->write('upload_error', $error[0]);
		}
		else
		{
			$gallery_icon_file_orig.=$error[1];
			$this->resizeImage($gallery_icon_file_orig, $gallery_id, false, 'gallery');
			$this->deleteMovedFile($gallery_icon_file_orig);
			$this->setFlash(___("Picture uploaded.", true), FLASH_NOTICE_KEY);
			
			$this->Gallery->id = $gallery_id;
			$this->Gallery->saveField('modified', date( 'Y-m-d H:i:s'));
		}
		$this->redirect('/galleries/view/'.$gallery_id);
	}


	/**
	 * Handles gallery browsing
	 * $gallery_id => gallery identifier
	 * $option => sorting option
	 * REDIRECT: /galleries/$option
	 */
	function browse($option = "newest") {
		$this->pageTitle = ___("Scratch | Galleries", true);
		$this->modelClass = "Gallery";

		$limit = 10;
		$this->Pagination->show = $limit;
		if ($option == "newest") {
			$options = Array("sortBy"=>"changed", "direction"=> "DESC", "url"=>"/galleries/browse/newest");
			$order = "Gallery.changed DESC";
			$page = null;
			list($order,$limit,$page) = $this->Pagination->init("total_projects > 0 AND Gallery.visibility = 'visible'", Array(), $options);
		} elseif ($option == "largest") {
			$options = Array("sortBy"=>"total_projects", "direction"=> "DESC", "url"=>"/galleries/browse/largest");
			$order = "Gallery.total_projects DESC";
			$page = null;
			list($order,$limit,$page) = $this->Pagination->init("total_projects > 0", Array(), $options);
		} elseif ($option == "feature") {
			$this->modelClass="FeaturedGallery";
			$options = Array("sortBy"=>"timestamp", "direction"=> "DESC", "url"=>"/galleries/browse/feature");
			list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
			$this->FeaturedGallery->bindGallery();
			$featured_record = $this->FeaturedGallery->findAll("", NULL, $order, $limit, $page);
		} elseif ($option == "clubbed") {
			$this->modelClass="ClubbedGallery";
			$options = Array("sortBy"=>"created_at", "direction"=> "DESC", "url"=>"/galleries/browse/clubbed");
			list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
			$this->ClubbedGallery->bindGallery();
			$clubbed_record = $this->ClubbedGallery->findAll("", NULL, $order, $limit, $page);
		}

		if ($option == "feature") {
			$featured_record = $this->finalize_galleries($featured_record);
			$this->set('data',$featured_record);
		} elseif ($option == "clubbed") {
			$clubbed_record = $this->finalize_galleries($clubbed_record);
			$this->set('data', $clubbed_record);
		} else {
			$data = $this->Gallery->findAll("total_projects > 0 AND Gallery.visibility = 'visible'", NULL, $order, $limit, $page);// display only galleries which have atleast 1 project
			$data = $this->set_galleries($data);
			$data = $this->finalize_galleries($data);
			
			$this->set('data',$data);
		}

		$this->set('option', $option);
		$this->render('explorer');
	}
	
	/**
	 * Compare helper for timestamp
	 * $x
	 * $y
	 * RETURN: -1 or 1 depending on comparison
	 */
	function compareAdded($x, $y)
	{
		$this->autoRender = false;
		if ($x['GalleryProject'][0]['timestamp'] == $y['GalleryProject'][0]['timestamp'] )
 		 	return 0;
 		else if ($x['GalleryProject'][0]['timestamp'] > $y['GalleryProject'][0]['timestamp'] )
  			return -1;
 		else
 			 return 1;
	}

	/**
	 * Compare helper for created
	 * $x
	 * $y
	 * RETURN: -1 or 1 depending on comparison
	 */
	function compareCreated($x, $y)
	{
		$this->autoRender = false;
		if ($x['created'] == $y['created'] )
 		 	return 0;
 		else if ($x['created'] < $y['created'] )
  			return -1;
 		else
 			 return 1;

	}

	/**
	 * Compare helper for title
	 * $x
	 * $y
	 * RETURN: -1 or 1 depending on comparison
	 */
	function compareTitle($x, $y)
	{
		$this->autoRender = false;
 		return strnatcasecmp($x['name'], $y['name']);
	}

	/**
	 * Compare helper for users
	 * $x
	 * $y
	 * RETURN: -1 or 1 depending on comparison
	 */
	function compareUsers($x, $y)
	{
		$this->autoRender = false;
		return strnatcasecmp($x['User']['username'], $y['User']['username']);
	}

	/**
	* Helper function for ajax rendering of comments
	* @gallery_id => gallery identifier
	*
	*
	*/
	function renderComments($gallery_id, $current_page = null) {
		$current_page = null;
        $this->autoRender = false;
		
        $this->Gallery->id = $gallery_id;
        $gallery = $this->Gallery->read();
		
        if (empty($gallery)) exit();

        $owner_id = $gallery['User']['id'];
        $isLogged = $this->isLoggedIn();
        $user_id = $this->getLoggedInUserID();
        $comment_data = $this->set_comments($gallery_id, $owner_id, $user_id, $isLogged);
		$this->set_comment_errors(array());

        $this->set('comments', $comment_data['comments']);
		$this->set('ignored_commenters', $comment_data['ignored_commenters']);
        $this->set('ignored_comments', $comment_data['ignored_comments']);
        $this->set('isLogged', $isLogged);
        $this->set('isThemeOwner', $this->getLoggedInUserID() == $owner_id);
		$this->set('theme_id', $gallery_id);
		
        $this->render('render_comments_ajax', 'ajax');
	}


	/**
	 * Renders a gallery page's content
	 * @param $gallery_id => gallery identifier
	 * @param $option => sorting option
	 * @param $criteria => ASC or DESC
	 */
    function view($gallery_id = null, $option="added", $criteria = null) {
		$this->autoRender = false;
		$isLogged = $this->isLoggedIn();
        $isAdmin = $this->isAdmin();
		$user_id = $this->getLoggedInUserID();
		
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->find("Gallery.id = $gallery_id", null, null, null, null, null, "overload");
        $content_status = $this->getContentStatus();

        $timestamp = $gallery['Gallery']['timestamp'];
		$actual_creation_date = friendlyDate($timestamp);
        
        $isFeatured = false;
        $isClubbed  = false;
        
		if (empty($gallery)) {
			$this->cakeError('error404');
		}
        else {
			if ($gallery['Gallery']['visibility'] != 'visible' && !$isAdmin) {
				$this->cakeError('error404');
			}
			
            $isFeatured = $this->FeaturedGallery->hasAny("gallery_id = $gallery_id");
            $isClubbed  = $this->ClubbedGallery->hasAny("gallery_id  = $gallery_id");

            if ($gallery['Gallery']['status'] != "safe") {
				if ($content_status == "safe") {
					if (!$isFeatured && !$isClubbed) {
						$this->cakeError('error404');
					}
				}
			}
		}

        $gallery = $this->finalize_gallery($gallery);
		$page = $this->set_gallery_projects_full($gallery_id, $option, $criteria);
        $owner_id = $gallery['Gallery']['user_id'];
        $isMine = ($user_id == $owner_id);
        $this->set('theme', $gallery['Gallery']);
        $this->set('theme_id', $gallery_id);
        $this->set('gallery_id', $gallery_id);
        $this->set('isThemeOwner', $isMine);
        $this->set('theme_owner', $gallery['User']);
        $this->set('gallery', $gallery);
        $this->set('actual_creation_date', $actual_creation_date);

        $user_status = 'normal';
        $user_name = '';
        if($user_id) {
            $current_user   = $this->User->find("id = $user_id");
			$user_status    = $current_user['User']['status'];
            $user_name      = $current_user['User']['username'];
        }
        $this->set('session_username', $user_name);
        $this->set('user_status', $user_status);
        
        //commments
        $comment_id = null;
        if(isset($this->params['url']['comment'])) {
            $comment_id = $this->params['url']['comment'];
        }
        $comment_data = $this->set_comments($gallery_id, $owner_id, $user_id, $isLogged, $comment_id);
        $this->set_comment_errors(array());
        $this->set('comments', $comment_data['comments']);
        $this->set('ignored_commenters', $comment_data['ignored_commenters']);
        $this->set('ignored_comments', $comment_data['ignored_comments']);
        $this->set('single_thread', $comment_data['single_thread']);

        if($comment_id) {
            $comment_level = 0;
            if($comment_data['comments'][0]['Gcomment']['reply_to']!=-100) {
               $parent = $this->Gcomment->field('reply_to', 'id = '. $comment_data['comments'][0]['Gcomment']['reply_to']);
               if($parent == -100) {
                   $comment_level = 1;
               }
               else {
                   $comment_level = 2;
               }
            }
            $this->set('comment_level', $comment_level);
            $this->render('comment_thread','scratchr_themepage');
            return;
        }

        $url = strtolower(env('SERVER_NAME'));
		$feed_link  = "/feeds/getRecentGalleryProjects/$gallery_id";
        $gallery_usage = $gallery['Gallery']['usage'];
        
        //gallery membership
        $isPublic = ($gallery['Gallery']['type'] == 0);
        $isFriend = false;
		$isThemeMember = false;
		$membership_type = 10;
        $isUserIgnored = false;
        
		if ($user_id) {
			$member_record = $this->GalleryMembership->find("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.user_id = $user_id");
			if (!empty($member_record)) {
				$membership_type = $member_record['GalleryMembership']['type'];
				$membership_rank = $member_record['GalleryMembership']['rank'];
				if ($membership_rank == 'member') {
					$isThemeMember = true;
				}
			}
            if ($gallery_usage == 'friends') {
                $friend = $this->Relationship->find("Relationship.user_id = $owner_id AND Relationship.friend_id = $user_id");
                if (!empty($friend)) {
                    $isFriend = true;
                }
            }

            //if the logged in user is in gallery owner's ignored list
            $isUserIgnored = $this->IgnoredUser->find('first',
                                array(
                                'conditions'=> array('blocker_id' => $owner_id, 'user_id' => $user_id),
                                'fields' => array('id')
                                )
                            );
            $isUserIgnored = !empty($isUserIgnored);
		}

        //tags
		$tags = $this->set_tags($gallery_id, $user_id, $isLogged);
		
		//sets flags and the last altered admin's name
		$flags = $this->GalleryFlag->find("gallery_id = $gallery_id");
        $admin_name = "None";
        if(!empty($flags)) {
            if ($flags['GalleryFlag']['admin_id'] != 0) {
                $admin_name = $this->User->field('username', 'id = ' . $flags['GalleryFlag']['admin_id']);
            }
        }

        //if there was an upload error
		$upload_error = $this->Session->read('upload_error');
		if(!empty($upload_error)) {
			$this->set('upload_error', $upload_error);
			$this->Session->del('upload_error');
		}

		$this->set('sessionUID', $user_id);
        $this->set('isLogged', $isLogged);
        $this->set('gallery_usage', $gallery_usage);
        $this->set('status', $gallery['Gallery']['status']);
        $this->set('feed_link', $feed_link);
        $this->set('isClubbed', $isClubbed);
        $this->set('isFeatured', $isFeatured);
        $this->set('isThemeMember', $isThemeMember);
		$this->set('isPublic', $isPublic);
        $this->set('membership_type', $membership_type);
        $this->set('isFriend', $isFriend);
		$this->set('isMine', $isMine);
        $this->set('isUserIgnored', $isUserIgnored);
		$this->set('admin_name', $admin_name);
		$this->set('flags', $flags);
		$this->set('page', $page);
		$this->set('gallery_tags', $tags);
		$this->set('option', $option);
		
		$this->render('themepage','scratchr_themepage');
    }


/**
	 * Helper for managing ajax enabled pagination of projects
	 * @param $gallery_id => gallery identifier
	 * @param $option => sorting option
	 * @param $criteria => ASC or DESC
	 */
    function renderProjects($gallery_id=null, $option=null, $criteria = null) {
		$this->autoRender = false;

		$this->GalleryProject->bindProject();
		$this->Gcomment->bindUser();
		$this->modelClass = "GalleryProject";
		$total_projects = $this->GalleryProject->findCount("gallery_id = $gallery_id");
		$this->Gallery->id = $gallery_id;
		$current_gallery = $this->Gallery->read();
		$gallery = $current_gallery;
		$content_status = $this->getContentStatus();

		$sessionUID = $this->getLoggedInUserID();
		$isThemeOwner = $gallery['User']['id'] == $sessionUID;
		$current_user = $this->User->read();

		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$feed_link = "/feeds/getRecentGalleryProjects/$gallery_id";
		
		
		$this->set_gallery_projects_full($gallery_id, $option, $criteria);
		$this->set('feed_link', $feed_link);
		$this->set('session_username', $current_user['User']['username']);
		$this->set('isThemeOwner', $isThemeOwner);
		$this->set('theme', $gallery['Gallery']);
		$this->set('theme_id', $gallery_id);
		$this->set('theme_owner', $gallery['User']);
		$this->set('option', $option);
		$this->set('isFeatured', $this->FeaturedGallery->hasAny("gallery_id = $gallery_id"));
		$this->set('isClubbed', $this->ClubbedGallery->hasAny("gallery_id = $gallery_id"));
		$this->render('render_projects_ajax', 'ajax');
		return;
    }
	
	/**
	 * Counts the number of projects for each gallery in the database
	 * and stores it in a separate field in the galleries table
	 */
    function countprojects() {
	  $count = 0;
      $galleries = $this->Gallery->findAll();
	  foreach($galleries as $gallery) {
	  	$galleryid = $gallery['Gallery']['id'];
	  	$totalProjects = $this->GalleryProject->findCount("gallery_id = $galleryid");

		$this->Gallery->id = $galleryid;
		if($this->Gallery->saveField("total_projects",$totalProjects)) {
			$count++;
		}
	  }
	  $this->set('count',$count);
	  $this->render('countprojects');
    }

	/**
 	* Allows users to add projects to gallery that they created or have bookmarked
	* @param $project_id => project identifier
	* AJAX: projectsetgallery_ajax
 	*/
	function addtogallery($project_id, $option = "owner") {
		$this->autoRender=false;
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$this->Project->bindUser();
		$project_owner = $project['User']['id'];
		$logged_id = $this->getLoggedInUserID();
		$isProjectOwner = $project_owner == $logged_id;
		
		$gallery_count = $this->Gallery->findCount("Gallery.user_id = $logged_id");
		if ($gallery_count == 0) {
			$isGalleryOwner = false;
		} else {
			$isGalleryOwner = true;
		}
		
		$this->modelClass = 'GalleryMembership';
		$options = Array("url"=>"/galleries/"."addtogallery"."/".$project_id);
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);
		
		$final_memberships = Array();
		if ($option == "owner") {
			$all_memberships = $this->GalleryMembership->findAll("GalleryMembership.user_id = $logged_id AND GalleryMembership.type = 0");
			foreach ($all_memberships as $membership) {
				$final_membership = $membership;
				$gallery_id = $membership['GalleryMembership']['gallery_id'];
				$project_record = $this->GalleryProject->findCount("GalleryProject.gallery_id = $gallery_id AND GalleryProject.project_id = $project_id");
				if ($project_record > 0) {
					$final_membership['Gallery']['exists'] = true;
				} else {
					$final_membership['Gallery']['exists'] = false;
				}
				array_push($final_memberships, $final_membership);
			}
		} elseif ($option == "public") {
			$all_memberships = $this->GalleryMembership->findAll("GalleryMembership.user_id = $logged_id AND GalleryMembership.rank = 'bookmarker' AND Gallery.usage = 'public'");
			foreach ($all_memberships as $membership) {
				$final_membership = $membership;
				$gallery_id = $membership['GalleryMembership']['gallery_id'];
				$project_record = $this->GalleryProject->findCount("GalleryProject.gallery_id = $gallery_id AND GalleryProject.project_id = $project_id");
				if ($project_record > 0) {
					$final_membership['Gallery']['exists'] = true;
				} else {
					$final_membership['Gallery']['exists'] = false;
				}
				array_push($final_memberships, $final_membership);
			}
		} elseif ($option == "memberof") {
			$all_memberships = $this->GalleryMembership->findAll("GalleryMembership.user_id = $logged_id AND GalleryMembership.rank = 'member'");
			foreach ($all_memberships as $membership) {
				$final_membership = $membership;
				$gallery_id = $membership['GalleryMembership']['gallery_id'];
				$project_record = $this->GalleryProject->findCount("GalleryProject.gallery_id = $gallery_id AND GalleryProject.project_id = $project_id");
				if ($project_record > 0) {
					$final_membership['Gallery']['exists'] = true;
				} else {
					$final_membership['Gallery']['exists'] = false;
				}
				array_push($final_memberships, $final_membership);
			}
		}
		
		$this->set('isProjectOwner', $isProjectOwner);
		$this->set('isGalleryOwner', $isGalleryOwner);
		$this->set('data', $final_memberships);
		$this->set('user_name', $project['User']['username']);
		$this->set('project_id', $project_id);
		$this->set('project_name', $project['Project']['name']);
		$this->set('option', $option);
		
		$this->render('projectsetgallery_ajax', 'ajax');
        return;
	}

	/**
 	* High level interface for adding projects (members only)
	* @param $gallery_id => gallery identifier
	* AJAX: galleryaddproject_ajax
 	*/
	function addprojectmember($gallery_id, $overload_page = null) {
		$this->autoRender=false;

		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];

		$user_id = $this->getLoggedInUserID();
		$this->User->id = $user_id;
		$user = $this->User->read();
		$user_name = $user['User']['username'];
		$user_url = $user['User']['urlname'];
		$this->modelClass = 'GalleryProject';
		$this->Project->bindUser();

		$this->PaginationSecondary->ajaxDivUpdate = "ajax_pagination_tertiary";
		$this->PaginationSecondary->show = 60;

		$this->modelClass = "Project";
		$options = Array("sortBy"=>"created", "sortByClass" => "Project",
						"direction"=> "DESC", "url"=>"/galleries/renderAddProjects/" . $gallery_id);

		if ($overload_page == null) {
			list($order,$limit,$page) = $this->PaginationSecondary->init("user_id = $user_id", Array(), $options);
			$original = $this->Project->findAll("user_id = $user_id", null, $order, $limit, $page);
		} else {
			$parameters = Array("page" => $overload_page);
			$this->PaginationSecondary->paramStyle = "pretty";
			list($order,$limit,$page) = $this->PaginationSecondary->init("user_id = $user_id", $parameters, $options);
			$original = $this->Project->findAll("user_id = $user_id", null, $order, $limit, $overload_page);
			$page = $overload_page;
		}


		$data = array();
		$selection = array();
		$counter = 0;
		$final = array();
		$counter = 0;

		foreach ($original as $project) {
			$project_id = $project['Project']['id'];
			$record = $this->GalleryProject->findAll("project_id = $project_id AND gallery_id = $gallery_id");
			if (empty($record)) {
				$temp_project = $project;
				$temp_project['GalleryProject']['exists'] = false;
				$final[$counter] = $temp_project;
				$counter++;
			} else {
				$temp_project = $project;
				$temp_project['GalleryProject']['exists'] = true;
				$final[$counter] = $temp_project;
				$counter++;
			}
		}

		$existing_record = $this->GalleryProject->findAll("gallery_id = $gallery_id");

		$this->set('page', $page);
		$this->set('user_name', $user_name);
		$this->set('user_url', $user_url);
		$this->set('data', $final);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery_name', $gallery_name);

		$this->render('galleryaddproject_ajax', 'ajax');
        return;
	}

	/**
	* Helper function for rendering add projects popup
	**/
	function renderAddProjects($gallery_id) {
		$this->autoRender=false;

		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];

		$user_id = $this->getLoggedInUserID();
		$this->User->id = $user_id;
		$user = $this->User->read();
		$user_name = $user['User']['username'];
		$user_url = $user['User']['urlname'];
		$this->modelClass = 'GalleryProject';
		$this->Project->bindUser();

		$this->PaginationSecondary->ajaxDivUpdate = "ajax_pagination_tertiary";
		$this->PaginationSecondary->show = 60;

		$this->modelClass = "Project";
		$options = Array("sortBy"=>"created", "sortByClass" => "Project",
						"direction"=> "DESC", "url"=>"/galleries/renderAddProjects/" . $gallery_id);
		list($order,$limit,$page) = $this->PaginationSecondary->init("user_id = $user_id", Array(), $options);
		$this->GalleryProject->bindHABTMProject(null, $order, $limit, $page);
		$original = $this->Project->findAll("user_id = $user_id", null, $order, $limit, $page);

		$data = array();
		$selection = array();
		$counter = 0;
		$final = array();
		$counter = 0;

		foreach ($original as $project) {
			$project_id = $project['Project']['id'];
			$record = $this->GalleryProject->findAll("project_id = $project_id AND gallery_id = $gallery_id");
			if (empty($record)) {
				$temp_project = $project;
				$temp_project['GalleryProject']['exists'] = false;
				$final[$counter] = $temp_project;
				$counter++;
			} else {
				$temp_project = $project;
				$temp_project['GalleryProject']['exists'] = true;
				$final[$counter] = $temp_project;
				$counter++;
			}
		}


		$existing_record = $this->GalleryProject->findAll("gallery_id = $gallery_id");

		$this->set('page', $page);
		$this->set('user_name', $user_name);
		$this->set('user_url', $user_url);
		$this->set('data', $final);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery_name', $gallery_name);

		$this->render('galleryaddproject_ajax', 'ajax');
        return;
	}
	/**
 	* High level interface for removing projects (members only)
	* @param $gallery_id => gallery identifier
	* AJAX: galleryremoveproject_ajax
 	*/
	function removeprojectmember($gallery_id) {
		$this->autoRender=false;
		$this->Pagination->ajaxAutoDetect = true;
		$this->Pagination->ajaxDivUpdate = 'gallery_removeproject';
		$options = Array("url"=>"/galleries/"."removeprojectmember"."/".$gallery_id);
		$this->Pagination->sortByClass = "Project";
		list($order,$limit,$page) = $this->Pagination->init("", Array(), $options);

		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];

		$user_id = $this->getLoggedInUserID();
		$this->User->id = $user_id;
		$user = $this->User->read();
		$user_name = $user['User']['username'];
		$user_url = $user['User']['urlname'];
		$this->modelClass = 'GalleryProject';
		$this->GalleryProject->bindHABTMProject(null, $order, $limit, $page);
		$this->Project->bindUser();

		$data = array();
		$final = array();
		$counter = 0;
		$data = $this->Project->findAll("user_id = $user_id", NULL, $order, $limit, $page, NULL, $this->getContentStatus());
		$existing_record = $this->GalleryProject->findAll("gallery_id = $gallery_id");
		$counter = 0;

		foreach ($existing_record as $galleryproject):
			foreach($data as $project):
			$galleryproject_id = $galleryproject['GalleryProject']['project_id'];
			$project_id = $project['Project']['id'];
			if ($project_id == $galleryproject_id) {
				$final[$counter] = $project;
				$counter++;
			}
			endforeach;
		endforeach;

		$this->set('user_name', $user_name);
		$this->set('user_url', $user_url);
		$this->set('data', $final);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery_name', $gallery_name);

		$this->render('galleryremoveproject_ajax', 'ajax');
        return;
	}

	/*
 	* Adds a project for members
	* @param $gallery_id => gallery identifier
	* @param $project_id => project identifier
	* REDIRECT: /galleries/addprojectmember/$gallery_id
 	*/
	function addmyproject($gallery_id, $project_id, $current_page = 1) {
		$this->autoRender=false;
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$project_name = $project['Project']['name'];
		$projects = $this->GalleryProject->findAll("gallery_id = $gallery_id AND project_id = $project_id");

		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];

		$UID = $this->getLoggedInUserID();
		$gallery_type = $gallery['Gallery']['type'];
		$project_count = $gallery['Gallery']['total_projects'];

		if (empty($projects)) {
			$info = Array('GalleryProject' => Array('id' => null, 'gallery_id' => $gallery_id, 'project_id' => $project_id));
			$this->GalleryProject->save($info);
			$membership_record = $this->GalleryMembership->findAll("GalleryMembership.user_id = $UID AND gallery_id = $gallery_id");
			if (empty($membership_record)) {
				//$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $UID, 'gallery_id' => $gallery_id, 'type' => 4, 'rank' => 'contributor'));
				//$this->GalleryMembership->save($info);
			}
			$this->updateGallery($gallery_id);
			$this->Gallery->saveField("total_projects", $project_count + 1);
			//$this->setFlash("$project_name " . ___('successful added to', true) . " $gallery_name", FLASH_NOTICE_KEY);
			$this->notify('project_added_to_gallery', $project['Project']['user_id'],
							array('project_id' => $project_id,
								'gallery_id' => $gallery_id));
			$this->redirect('/galleries/'.'addprojectmember'.'/'.$gallery_id . '/' . $current_page);
		} else {
			$duplicate = $this->GalleryProject->find("gallery_id = $gallery_id AND project_id = $project_id");
			$galleryproject_id = $duplicate['GalleryProject']['id'];
			$this->Gallery->saveField("total_projects", $project_count - 1);
			$this->GalleryProject->del($galleryproject_id);
			$this->updateGallery($gallery_id);
			//$this->setFlash("$project_name" . ___('successful removed from', true) . " $gallery_name", FLASH_NOTICE_KEY);
			$this->redirect('/galleries/'.'addprojectmember' ."/". $gallery_id . '/' . $current_page);
		}
	}

	/*
 	* Removes a project for members
	* @param $gallery_id => gallery identifier
	* @param $project_id => project identifier
	* REDIRECT: /galleries/removeprojectmember/$gallery_id
 	*/
	function removemyproject($gallery_id, $project_id) {
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$project_name = $project['Project']['name'];
		$projects = $this->GalleryProject->findAll("gallery_id = $gallery_id AND project_id = $project_id");
		$galleryproject = $this->GalleryProject->find("GalleryProject.gallery_id = $gallery_id AND GalleryProject.project_id = $project_id");

		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$this->Gallery->remove_project($gallery_id);
		
		if ($galleryproject) {
			$galleryproject_id = $galleryproject['GalleryProject']['id'];
			$this->GalleryProject->del($galleryproject_id);
			$this->updateGallery($gallery_id);
			//$this->setFlash("$project_name" . ___('successful removed from', true) . " $gallery_name", FLASH_NOTICE_KEY);
			$this->redirect('/galleries/'.'removeprojectmember'.'/'.$gallery_id);
		} else {
			//$this->setFlash(___("This gallery does not contain", true) . " $project_name", FLASH_NOTICE_KEY);
			$this->redirect('/galleries/'.'removeprojectmember' ."/". $gallery_id);
		}
	}

	/*
 	* Adds a project for owners
	* @param $gallery_id => gallery identifier
	* @param $project_id => project identifier
	* REDIRECT: /galleries/addtogallery/$project_id
 	*/
	function addproject($gallery_id, $project_id, $option = "owner") {
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$project_name = $project['Project']['name'];
		$projects = $this->GalleryProject->find("gallery_id = $gallery_id AND project_id = $project_id");
		$user_id = $project['Project']['user_id'];

		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$project_count = $gallery['Gallery']['total_projects'];
		$galleryurl = "/galleries/view/$gallery_id";

		$this->User->id = $user_id;
		$user = $this->User->read();
		$user_name = $user['User']['username'];
		$projecturl = "/projects/$user_name/$project_id";
		$userurl = "/users/$user_name";

		if ($projects == null) {
			$info = Array('GalleryProject' => Array('id' => null, 'gallery_id' => $gallery_id, 'project_id' => $project_id));
			$this->GalleryProject->save($info);
			$this->updateGallery($gallery_id);
			$this->Gallery->saveField("total_projects", $project_count + 1);
			//$this->setFlash(___("Project successful added to", true) . " $gallery_name", FLASH_NOTICE_KEY);
			$this->notify('project_added_to_gallery', $project['Project']['user_id'],
							array('project_id' => $project_id,
								'gallery_id' => $gallery_id));
												  
			$this->redirect('/galleries/'.'addtogallery' ."/". $project_id . "/" . $option);
		} else {
			$this->GalleryProject->del($projects['GalleryProject']['id']);
			$this->updateGallery($gallery_id);
			$this->Gallery->saveField("total_projects", $project_count - 1);
			//$this->setFlash(___("This gallery already contains", true) . " $project_name", FLASH_NOTICE_KEY);
			$this->redirect('/galleries/'.'addtogallery' ."/". $project_id . "/". $option);
		}
	}

	/*
 	* Removes a project for owners
	* @param $gallery_id => gallery identifier
	* @param $project_id => project identifier
	* REDIRECT: /galleries/view/$gallery_id
 	*/
	function removeproject($gallery_id, $project_id, $option = "") {
		$session_uid = $this->getLoggedInUserID();
		$galleryproject = $this->GalleryProject->find("gallery_id = $gallery_id AND project_id = $project_id");
		$this->Project->id = $project_id;
		$project = $this->Project->read();
		$project_owner_id = $project['Project']['user_id'];
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_owner_id = $gallery['Gallery']['user_id'];
		
		$isProjectOwner = ($project_owner_id == $session_uid);
		$isGalleryOwner = ($gallery_owner_id == $session_uid);
		$isAdmin = $this->isAdmin();
		
		if ($isAdmin || $isProjectOwner || $isGalleryOwner) {
			
		} else {
			$this->cakeError('error404');
		}

		$project_name = $project['Project']['name'];
		if (!$galleryproject) {
			$this->redirect('/galleries/view/'.$gallery_id . "/" . $option);
		} else {
			$this->Gallery->remove_project($gallery_id);
			$this->GalleryProject->del($galleryproject['GalleryProject']['id']);
			$this->updateGallery($gallery_id);
			$this->redirect('/galleries/view/'.$gallery_id . "/" . $option);
		}
	}

	/*
 	* Toggles predefined permissions when setting gallery permissions
	* @param $gallery_id => gallery identifier
	* @param $option => permission option
	* REDIRECT: /galleries/setpermissions/$gallery_id
 	*/
	//SELF = 1
	//EVERYONE = 0
	//FRIENDS = 3
	function predefinepermissions($gallery_id, $option = null) {
		$session_uid = $this->getLoggedInUserID();
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$owner_id = $gallery['Gallery']['user_id'];
		$this->Relationship->bindFriend();
		$this->GalleryMembership->bindUser();
		if (!($owner_id == $session_uid)) {
			if (!($this->isAdmin())) {
				$this->cakeError('error404');
			}
		}
		


		if ($option == "self") {
			$this->Gallery->saveField("type", 1);
			$this->Gallery->saveField("usage", "private");
			$members_record = $this->GalleryMembership->findAll("GalleryMembership.gallery_id = $gallery_id");
			foreach ($members_record as $member) {
				if ($member['GalleryMembership']['user_id'] == $owner_id) {

				} else {
					$this->GalleryMembership->del($member['GalleryMembership']['id']);
				}
			}
		} elseif ($option == "everyone") {
			$this->Gallery->saveField("type", 0);
			$this->Gallery->saveField("usage", "public");
			$members_record = $this->GalleryMembership->findAll("GalleryMembership.gallery_id = $gallery_id");
			foreach ($members_record as $member) {
				if ($member['GalleryMembership']['user_id'] == $owner_id) {

				} else {
					$this->GalleryMembership->del($member['GalleryMembership']['id']);
				}
			}
		} elseif ($option == "friends") {
			$this->Gallery->saveField("usage", "friends");
			$this->Gallery->saveField("type", 3);
			$members_record = $this->GalleryMembership->deleteAll("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.user_id != $owner_id");
			
			$relations = $this->Relationship->findAll("user_id = $owner_id");
			
			foreach($relations as $relation){
				$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $relation['Relationship']['friend_id'], 'gallery_id' => $gallery_id, 'type' => 3, 'rank' => 'member'));
				$this->GalleryMembership->save($info);
				$this->GalleryMembership->id = false;
			}
		
			
			
		} elseif ($option == "byinvite") {
			$this->Gallery->saveField("type", 4);
			$this->Gallery->saveField("usage", "byinvite");
		}

		$this->redirect('/galleries/'. 'setpermissions/' . $gallery_id);
	}

	/*
 	* High level interface for setting permissions in galleries
	* @param $gallery_id => gallery identifier
	* RENDER
 	*/
	function setpermissions($gallery_id) {
		$this->pageTitle = ___("Gallery | Who can access", true);
		$session_uid = $this->getLoggedInUserID();
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$gallery_type = $gallery['Gallery']['type'];
		$owner_id = $gallery['Gallery']['user_id'];

		$this->Relationship->bindFriend();
		$this->GalleryMembership->bindUser();
		if (!($owner_id == $session_uid)) {
			if (!$this->isAdmin()) {
				$this->cakeError('error404');
			}	
		}

		$friends_record = $this->Relationship->findAll("user_id = $owner_id");
		$members_record = $this->GalleryMembership->findAll("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.rank = 'member'");

		$members = array();
		$counter = 0;
		if (!empty($members_record)) {
			foreach ($members_record as $member) {
				if ($member['User']['id'] == $owner_id) {

				} else {
					$members[$counter] = $member['User'];
					$counter++;
				}
			}
		}

		$friends = array();
		$counter = 0;
		$duplicate = 0;
		if (!empty($friends_record)) {
			foreach ($friends_record as $friend) {
				$duplicate = 0;
				foreach ($members as $x) {
					if ($x['id'] == $friend['Friend']['id']) {
						$duplicate = 1;
					}
				}
				if ($duplicate == 1) {

				} else {
					$friends[$counter] = $friend['Friend'];
					$counter++;
				}
			}
		}

		$this->set('gallery_type', $gallery_type);
		$this->set('gallery_name', $gallery_name);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery', $gallery);
		$this->set('friends', $friends);
		$this->set('members', $members);
	}

	/*
 	* Target user becomes a member of this gallery
	* @param $gallery_id => gallery identifier
	* @param $user_id => user identifier
	* AJAX: gallerypermissions_ajax
 	*/
	function addgallerymember($gallery_id, $user_id) {
		$this->exitOnInvalidArgCount(2);
		$this->autoRender=false;

		$session_uid = $this->getLoggedInUserID();
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$owner_id = $gallery['Gallery']['user_id'];
		
		$this->Gallery->saveField("type", 4);
		$this->Gallery->saveField("usage", "byinvite");

		$this->User->id = $user_id;
		$newuser = $this->User->read();
		$newusername = $newuser['User']['username'];
		
		if (!($owner_id == $session_uid)) {
			$this->cakeError('error404');
		}

		$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'type' => 2, 'rank' => 'member'));
		$duplicate_record = $this->GalleryMembership->findAll("gallery_id = $gallery_id AND GalleryMembership.user_id = $user_id");

		$duplicate = 0;
		if (!empty($duplicate_record)) {
			$duplicate = 1;
		}

		$this->Relationship->bindFriend();
		$this->GalleryMembership->bindUser();
		$friends_record = $this->Relationship->findAll("user_id = $owner_id");
		$members_record = $this->GalleryMembership->findAll("gallery_id = $gallery_id AND GalleryMembership.rank = 'member'");

		$members = array();
		$counter = 0;
		if (!empty($members_record)) {
			foreach ($members_record as $member) {
				if ($member['User']['id'] == $owner_id) {

				} else {
					$members[$counter] = $member['User'];
					$counter++;
				}
			}
		}

		if ($duplicate == 1) {
			$this->setFlash("$newusername" . ___('is already a  member of', true) . " $gallery_name", FLASH_NOTICE_KEY);
		} else {
			$members[$counter] = $newuser['User'];
			$this->GalleryMembership->save($info);
		}

		$friends = array();
		$counter = 0;
		$duplicate = 0;
		if (!empty($friends_record)) {
			foreach ($friends_record as $friend) {
				$duplicate = 0;
				foreach ($members as $x) {
					if ($x['id'] == $friend['Friend']['id']) {
						$duplicate = 1;
					}
				}
				if ($duplicate == 1) {

				} else {
					$friends[$counter] = $friend['Friend'];
					$counter++;
				}
			}
		}
		
		$gallery_type = $gallery['Gallery']['type'];
		$this->set('gallery_type', 4);
		$this->set('gallery_name', $gallery_name);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery', $gallery);
		$this->set('friends', $friends);
		$this->set('members', $members);
		$this->render('gallerypermissions_ajax', 'ajax');
	}

	/*
 	* Removes target user as a member of target gallery
	* @param $gallery_id => gallery identifier
	* @param $user_id => user identifier
	* AJAX: gallerypermissions_ajax
 	*/
	function removegallerymember($gallery_id, $user_id) {
		$this->exitOnInvalidArgCount(2);
		$this->autoRender=false;

		$session_uid = $this->getLoggedInUserID();
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$owner_id = $gallery['Gallery']['user_id'];
		
		$this->Gallery->saveField("type", 3);
		if (!($owner_id == $session_uid)) {
			$this->cakeError('error404');
		}

		$this->User->id = $user_id;
		$newuser = $this->User->read();
		$newusername = $newuser['User']['username'];

		$this->Relationship->bindFriend();
		$this->GalleryMembership->bindUser();
		$friends_record = $this->Relationship->findAll("Relationship.user_id = $owner_id");
		$members_record = $this->GalleryMembership->findAll("GalleryMembership.gallery_id = $gallery_id");
		$membership_record = $this->GalleryMembership->find("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.user_id = $user_id AND GalleryMembership.rank = 'member'");

		$members = array();
		$counter = 0;
		if (!empty($members_record)) {
			foreach ($members_record as $member) {
				if($member['User']['id'] == $user_id || $member['User']['id'] == $owner_id) {

				} else {
					$members[$counter] = $member['User'];
					$counter++;
				}
			}
		}

		$friends = array();
		$counter = 0;
		$duplicate = 0;
		if (!empty($friends_record)) {
			foreach ($friends_record as $friend) {
				$duplicate = 0;
				foreach ($members as $x) {
					if ($x['id'] == $friend['Friend']['id'] && !($x['id'] == $user_id)) {
						$duplicate = 1;
					}
				}
				if ($duplicate == 1) {

				} else {
					$friends[$counter] = $friend['Friend'];
					$counter++;
				}
			}
		}

		$this->GalleryMembership->del($membership_record['GalleryMembership']['id']);

		$gallery_type = $gallery['Gallery']['type'];
		$this->set('gallery_type', 3);
		$this->set('gallery_name', $gallery_name);
		$this->set('gallery_id', $gallery_id);
		$this->set('gallery', $gallery);
		$this->set('friends', $friends);
		$this->set('members', $members);
		$this->render('gallerypermissions_ajax', 'ajax');
	}

	/*
 	* Bookmarks target gallery
	* @param $gallery_id => gallery identifier
	* REDIRECT: /galleries/view/$gallery_id
 	*/
	function bookmark($gallery_id) {
		$this->autoRender=false;

		$logged_id = $this->getLoggedInUserID();
		$this->User->id = $logged_id;
		$logged_user = $this->User->read();
		
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$owner_id = $gallery['Gallery']['user_id'];
		
		$isGalleryOwner = $owner_id == $logged_id;
		$isPublic = false;
		$isGalleryMember = false;
		$membership_type = -1;
		
		$membership_record = $this->GalleryMembership->find("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.user_id = $logged_id");
		if (empty($membership_record)) {
			$info = Array('GalleryMembership' => Array('id' => null, 'user_id' => $logged_id, 'gallery_id' => $gallery_id, 'type' => 3, 'rank' => 'bookmarker'));
			$this->GalleryMembership->save($info);
			$membership_type = 3;
		}
		
		if ($isGalleryOwner)
		{
			$this->set('isGalleryOwner', true);
		} else {
			$membership_record = $this->GalleryMembership->find("GalleryMembership.rank = 'member' AND GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.user_id = $logged_id");
			if (!empty($member_record)) {
				$membership_type = $member_record['GalleryMembership']['type'];
				$isGalleryMember = true;
			}
			if ($gallery['Gallery']['usage'] == 'public') {
				$isPublic = true;
			}
		}
		
		$friend_record = $this->Relationship->find("Relationship.user_id = $owner_id AND Relationship.friend_id = $logged_id");
		
		$gallery_usage = $gallery['Gallery']['usage'];
		
		if (empty($friend_record)) {
			$isFriend = 0;
		} else {
			if ($gallery_usage == 'friends') {
				$isFriend = 1;
			} else {
				$isFriend = 0;
			}
		}
		
		$this->set('gallery_id', $gallery_id);
		$this->set('membership_type', $membership_type);
		$this->set('isFriend', $isFriend);
		$this->set('isThemeOwner', $isGalleryOwner);
		$this->set('isThemeMember', $isGalleryMember);
		$this->set('isPublic', $isPublic);
		$this->set('membership_type', $membership_type);
		$this->render('user_actions_ajax', 'ajax');
	}

	/*
 	* Helper function for updating gallery info
	* @param $gallery_id => gallery identifier
	* REDIRECT: return
 	*/
	function updateGallery($gallery_id) {
		$this->autoRender=false;
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$current_time = time();
		$current_datetime = gmdate("Y-m-d H:i:s", $current_time);
		$this->Gallery->saveField("changed", $current_datetime);
		return;
	}

	 /**
     * Tag action on the given gallery
     * @param int $pid => project id
     */
    function tag($gallery_id) {
        $this->autoRender=false;
        $this->Gallery->bindUser();
        $this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();
		$isLogged = $this->isLoggedIn();

		$user_id= $this->getLoggedInUserID();
		$isLocked = $this->check_locked($user_id);

		if ($isLocked) exit();

        if (!$user_id) exit;

		if (!empty($this->params['form']['tag_textarea']))
		{
			//breaks down comma delimitted tags into an array
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
					$this->notify('inappropriate_gtag', $user_id,
									array('gallery_id' => $gallery_id),
									array($ntag));
					continue;
				}

                $tag_record = $this->Tag->find("name = '$ntag'");


                if (!empty($tag_record))
				{
				    $tag_id = $tag_record['Tag']['id'];
					$gallery_tags = $this->GalleryTag->findAll("GalleryTag.user_id = $user_id AND GalleryTag.tag_id = $tag_id AND GalleryTag.gallery_id = $gallery_id");

					if (empty($gallery_tags)) {
                        // create gallery_tag record
                        $this->GalleryTag->save(array('GalleryTag' => array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'tag_id' => $tag_id)));
                    }
				} else {
						// create tag record
						$this->Tag->save(array('Tag'=>array('id'=> null, 'name'=>$ntag)));
						$this->Tag->id=null; // otherwise things will be overridden
						$new_tag_id = $this->Tag->getLastInsertID();
						$this->GalleryTag->save(array('GalleryTag'=>array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'tag_id' => $new_tag_id)));
				}
			}
		}

		$gallery_tags = $this->GalleryTag->findAll("gallery_id = $gallery_id");
		$final_tags = Array();
		$all_tags = Array();
		$this->GalleryTag->bindTag();
		$counter = 0;
		foreach ($gallery_tags as $current_tag) {
			$tag_id = $current_tag['GalleryTag']['tag_id'];
			$current_tag_id = $current_tag['Tag']['id'];
			
			$current_tag = $this->set_tag($gallery_id, $user_id, $current_tag);
			
			if (in_array($current_tag_id, $all_tags)) {
			} else {
				array_push($all_tags, $current_tag_id);
				$final_tags[$counter] = $current_tag;
				$counter++;
			}
		}

		$this->set('isLogged', $isLogged);
		$this->set('gallery_id', $gallery_id);
        $this->set('tags', $final_tags);
		$this->set('isMine', $user_id == $gallery['User']['id']);
        $this->render('gallerytag_ajax', 'ajax');
        return;
    }

	/**
	 * Dissociates a GalleryTag from a gallery
	 * Does not delete the tag if no project associated with it
	 */
	function deltag($gallery_id, $gallery_tag_id)
	{
        $this->autoRender=false;
        $this->Gallery->bindUser();
        $this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();

		if (empty($gallery)) $this->cakeError('error404');

		if (!$this->isAdmin())
			if (!$this->activeSession($gallery['User']['id']))
				$this->cakeError('error404');


		$this->GalleryTag->del($gallery_tag_id);
		exit;
	}

	/** Marks a comment as inappropriate (AJAX)
	* @param int $gallery_id => gallery identifier
	* @param int $comment_id => comment identifier
	*/
	function markcomment($gallery_id, $comment_id, $current_page = null, $delete_simillar_comment=null) {
		$this->autoRender=false;
		if($delete_simillar_comment==1)
		$isdeleteAll =true;
		else
		$isdeleteAll =false;
		$isAdmin = $this->isAdmin();
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();

		$this->Gallery->bindUser();
		$this->Gallery->id=$gallery_id;

		$gallery = $this->Gallery->read();
		$urlname = $gallery['User']['urlname'];

		$this->Gcomment->id = $comment_id;
		$this->Gcomment->bindUser();
		$comment = $this->Gcomment->read();

		$gallery_creator = $gallery['User']['username'];
		$creator_id = $comment['Gcomment']['user_id'];
		$content = $comment['Gcomment']['content'];
		$isMine = ($user_id == $gallery['User']['id']);
		$creator = $this->User->find("User.id = '$creator_id'");
		$creatorname = $creator['User']['username'];
		$creatorname_href =TOPLEVEL_URL.'/users/'.$creator['User']['username'];
		$linked_creatorname = "<a href='$creatorname_href'>".$creator['User']['username']."</a>";
		$userflagger = $this->User->find("User.id = '$user_id'");
		$flaggername = $userflagger['User']['username'];
		$gallery_name = $gallery['Gallery']['name'];

        $mgcomment_record = $this->Mgcomment->findCount("user_id = $user_id AND comment_id = $comment_id");
		//checks to see if this user has already marked this comment previously
		if ($mgcomment_record == 0) {
			$this->Mgcomment->save(array('Mgcomment'=>array('id'=>null, 'user_id' => $user_id, 'comment_id' => $comment_id)));
/*
			$msg = "User '$flaggername' has flagged this comment by '$creatorname':\n$content\nhttp://scratch.mit.edu/projects/$project_creator/$pid";
			$subject= "Flagged comment under '$pname'";
			$this->Email->email('help@scratch.mit.edu',  $flaggername, $msg, $subject, 'caution@scratch.mit.edu', $userflagger['User']['email']);
*/
		}

		//checks to see if the comment has been flagged too many times
		$max_count = NUM_MAX_COMMENT_FLAGS;
		$inappropriate_count = $this->Mgcomment->findCount("comment_id = $comment_id");
		$gallery_creater_url =TOPLEVEL_URL.'/galleries/view/'.$gallery_id;
		if ($inappropriate_count > $max_count || $isMine || $isAdmin) {
			// Only do the deletion when it's the owner of the project flagging it
			if ($isMine) {
				$this->hide_gcomment($comment_id, "delbyusr");
                $this->Gcomment->deleteCommentsFromMemcache($gallery_id);
				$subject= "Comment deleted because it was flagged by creator of '$gallery_name'";
				$msg = "Comment by '$linked_creatorname' deleted because it was flagged by the project owner:\n$content\n $gallery_creater_url";
			} elseif ($isAdmin) {
				if($isdeleteAll)
				{
					$all_content = $this->Gcomment->findAll(array('content'=>$comment['Gcomment']['content']));
					
					foreach($all_content as $gcontent)
					{
                        $comment_id =$gcontent['Gcomment']['id'];
                        $content = $gcontent['Gcomment']['content'];
                        $this->hide_gcomment($comment_id, "delbyadmin");
                        $this->Gcomment->deleteCommentsFromMemcache($gcontent['Gcomment']['gallery_id']);
                        $subject= "Comment deleted because it was flagged by an admin";
                        $msg = "Comment by '$linked_creatorname' deleted because it was flagged by an admin:\n$content\n $gallery_creater_url";
                        $this->notify('gcomment_removed', $creator_id,
                                    array('gallery_id' => $gallery_id),
                                    array($content)
                                );
					
					}
				}
				else
				{
					$this->hide_gcomment($comment_id, "delbyadmin");
                    $this->Gcomment->deleteCommentsFromMemcache($gallery_id);
					$subject= "Comment deleted because it was flagged by an admin";
					$msg = "Comment by '$linked_creatorname' deleted because it was flagged by an admin:\n$content\n $gallery_creater_url";
					$this->notify('gcomment_removed', $creator_id,
								array('gallery_id' => $gallery_id),
								array($content)
							);
				}			
			}
			if ($inappropriate_count > $max_count) {
				$this->Mgcomment->bindUser();
				$linked_stringwflaggernames = "";
				$allflaggers = $this->Mgcomment->findAll("comment_id = $comment_id");
				foreach ($allflaggers as $flagger) {
					$user_href =TOPLEVEL_URL.'/users/'.$flagger['User']['username'];
					$linked_stringwflaggernames .="<a href ='$user_href'>";
					$linked_stringwflaggernames .= $flagger['User']['username'] . "</a>,";
				}
				
				$subject = "Attention: more than $max_count users have flaggeed $creatorname's comment on the gallery: '$gallery_name'";
				$msg = "Users  $linked_stringwflaggernames have flagged this comment by  $linked_creatorname :\n$content\n $gallery_creater_url";
				
			}
			
			$this->Email->email(REPLY_TO_FLAGGED_GCOMMENT,  $flaggername, $msg, $subject, TO_FLAGGED_GCOMMENT, $userflagger['User']['email']);
		}
		
		//$this->set_comment_errors($errors);
		$this->set('comment', $this->Gcomment->find("Gcomment.id = $comment_id"));
		$this->set('comment_id', $comment_id);
		$this->set('urlname', $urlname);
		$this->set('isLogged', $isLogged);
		$this->set('gallery_id', $gallery_id);
		$this->set('isMine', $isMine);
		$this->set('mgcomments',$this->Mgcomment->findAll("user_id = $user_id"));
		$this->render('gallerymarkcomment_ajax', 'ajax');
	}

	/**
	/	Change gallery safe level
	/ 	$safe_level - see database gallery.safe
	**/
	function set_status($gallery_id, $safe_level) {
		$this->autoRender=false;
		$this->checkPermission('galleries_view_permission');
		$user_id = $this->getLoggedInUserID();
		$this->Gallery->id = $gallery_id;
		$this->Gallery->bindUser();
		$gallery = $this->Gallery->read();
		$username = $gallery['User']['username'];
		$this->Gallery->saveField("status", $safe_level);


		if ($this->GalleryFlag->findCount("gallery_id = $gallery_id") == 0) {
			$info = Array('GalleryFlag' => Array('id' => null, 'gallery_id' => $gallery_id, 'admin_id' => $user_id));
			$this->GalleryFlag->save($info);
		} else {
			$flags = $this->GalleryFlag->find("gallery_id = $gallery_id");
			$this->GalleryFlag->id = $flags['GalleryFlag']['id'];
			$gallery_flags = $this->GalleryFlag->read();
			$this->GalleryFlag->saveField('admin_id', $user_id);
		}

		$final_flags = $this->GalleryFlag->find("gallery_id = $gallery_id");

		$admin_id = $final_flags['GalleryFlag']['admin_id'];
		if ($admin_id == 0) {
			$admin_name = "None";
		} else {
			$admin = $this->User->find("id = $admin_id");
			$admin_name = $admin['User']['username'];
		}

		$this->set('admin_name', $admin_name);
		$this->set('status', $safe_level);
		$this->set('flags', $final_flags);
		$this->set('gallery_id', $gallery_id);
		$this->render('gallery_set_attribute_ajax', 'ajax');
	}

	/**
	/* Sets the value of an attribute for a gallery
	**/
	function set_attribute($gallery_id, $attribute, $state) {
		$this->autoRender=false;
		$this->checkPermission('galleries_view_permission');
		$gallery= $this->Gallery->find("Gallery.id = $gallery_id");
		$user_id = $this->getLoggedInUserID();

		if ($this->GalleryFlag->findCount("gallery_id = $gallery_id") == 0) {
			$info = Array('GalleryFlag' => Array('id' => null, 'gallery_id' => $gallery_id, 'admin_id' => $user_id));
			$this->GalleryFlag->save($info);
		} else {
			$flags = $this->GalleryFlag->find("gallery_id = $gallery_id");
			$this->GalleryFlag->id = $flags['GalleryFlag']['id'];
			$gallery_flags = $this->GalleryFlag->read();
			$this->GalleryFlag->saveField('admin_id', $user_id);
			$this->GalleryFlag->saveField($attribute, $state);
		}

		$final_flags = $this->GalleryFlag->find("gallery_id = $gallery_id");
		$final_flags['GalleryFlag'][$attribute] = $state;

		$admin_id = $final_flags['GalleryFlag']['admin_id'];
		if ($admin_id == 0) {
			$admin_name = "None";
		} else {
			$admin = $this->User->find("id = $admin_id");
			$admin_name = $admin['User']['username'];
		}

		$this->set('admin_name', $admin_name);
		$this->set('status', $gallery['Gallery']['status']);
		$this->set('flags', $final_flags);
		$this->set('gallery_id', $gallery_id);
		$this->render('gallery_set_attribute_ajax', 'ajax');
		return;
	}

	/*
	* markTag - marks a gallery_tag as inappropriate
	* $gallery_tag_id - gallery_tag id
	*
	*/
	function markTag($gallery_tag_id) {
		$this->autoRender=false;
		
		$user_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$user = $this->User->find("User.id = $user_id");
		$user_name = $user['User']['username'];
		
		$this->GalleryTag->id = $gallery_tag_id;
		$gallery_tag = $this->GalleryTag->read();
		
		$gallery_id = $gallery_tag['GalleryTag']['gallery_id'];
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		$gallery_owner_id = $gallery['Gallery']['user_id'];
		
		$gallery_tag_id = $gallery_tag['GalleryTag']['id'];
		$tag_id = $gallery_tag['GalleryTag']['tag_id'];
		$this->Tag->id = $tag_id;
		$tag = $this->Tag->read();
		$tag_name = $tag['Tag']['name'];
		
		if (!$isLogged) {
			$this->cakeError('error404');
		}
		
		$mark_records = $this->TagFlag->findAll("user_id = $user_id AND tag_id = $tag_id");
		if (empty($mark_records)) {
			$info = Array('TagFlag' => Array('id' => null, 'user_id' => $user_id, 'tag_id' => $tag_id));
			$this->TagFlag->save($info);
		}

		$removed = false;
		if ($gallery_owner_id == $user_id) {
			$removed = true;
			$this->GalleryTag->del($gallery_tag_id);
			$subject = "Attention: the owner of the gallery '$gallery_name' has flagged the tag '$tag_name'";
			$msg = "$tag_name has been removed because it was flagged by gallery owner $user_name on \n". TOPLEVEL_URL."/galleries/view/$gallery_id";
		} else {
			$stringwflaggernames = "";
			$mark_count = $this->TagFlag->findCount("tag_id = $tag_id") + 1;
			
			if ($mark_count > NUM_MAX_TAG_FLAGS) {
				$removed = true;
				$gallery_tags = $this->GalleryTag->findAll("GalleryTag.gallery_id = $gallery_id AND tag_id = $tag_id");
				
				foreach ($gallery_tags as $current_tag) {
					$current_id = $current_tag['GalleryTag']['id'];
					$this->GalleryTag->del($current_id);
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
				$subject = "Attention: more than $mark_count users have flagged the tag '$tag_name' on $gallery_name";
				$msg = "Users '$stringwflaggernames' have flagged the tag-$tag_name on \n". TOPLEVEL_URL."/galleries/view/$gallery_id";
			}
		}

		$gallery_tag = $this->set_tag($gallery_id, $user_id, $gallery_tag, 1);

		$this->set('gallery_id', $gallery_id);
		$this->set('removed', $removed);
		$this->set('tag_id', $tag_id);
		$this->set('tag', $gallery_tag);
		$this->render('mark_tag_ajax', 'ajax');
	}
	
	/**
	* Helper for trimming tag records
	**/
	function set_tag($gallery_id, $user_id, $tag, $overload = 0) {
		$current_tag = $tag;
		$tag_id = $current_tag['GalleryTag']['tag_id'];

		$tagged_record = $this->GalleryTag->findAll("GalleryTag.user_id = $user_id AND GalleryTag.tag_id = $tag_id");
		$flagged_record = $this->TagFlag->findAll("TagFlag.user_id = $user_id AND tag_id = $tag_id");
		
		if (empty($flagged_record)) {
			$flagged = false;
		} else {
			$flagged = true;
		}

		if (empty($tagged_record)) {
			$tagged = false;
		} else {
			$tagged = true;
		}
		
		$tag_count = $this->GalleryTag->findCount("gallery_id = $gallery_id AND tag_id = $tag_id");
		$tag_size = $this->getTagSize($tag_count);
		$current_tag['GalleryTag']['size'] = $tag_size;
		
		if ($overload == 0) {
			$current_tag['GalleryTag']['flagged'] = $flagged;
			$current_tag['GalleryTag']['tagged'] = $tagged;
		} elseif ($overload == 1) {
			$current_tag['GalleryTag']['flagged'] = true;
			$current_tag['GalleryTag']['tagged'] = $tagged;
		} elseif ($overload == 2) {
			$current_tag['GalleryTag']['flagged'] = $flagged;
			$current_tag['GalleryTag']['tagged'] = true;
		}
		
		return $current_tag;
	}
	/**
	* Tag a gallery with a tag that has already been used
	**/
	function upgradeTag($gallery_tag_id) {
		$this->autoRender = false;

		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();

		$this->GalleryTag->id = $gallery_tag_id;
		$gallery_tag = $this->GalleryTag->read();

		$gallery_id = $gallery_tag['GalleryTag']['gallery_id'];
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();

		$tag_id = $gallery_tag['GalleryTag']['tag_id'];
		$this->Tag->id = $tag_id;
		$tag = $this->Tag->read();

		$gallery_tag_record = $this->GalleryTag->findAll("GalleryTag.user_id = $user_id AND GalleryTag.gallery_id = $gallery_id AND GalleryTag.tag_id = $tag_id");
		if (empty($gallery_tag_record)) {
			$this->GalleryTag->save(array('GalleryTag' => array('id' => null, 'user_id' => $user_id, 'gallery_id' => $gallery_id, 'tag_id' => $tag_id)));
            $this->GalleryTag->id=null;
		}

		$gallery_tag = $this->set_tag($gallery_id, $user_id, $gallery_tag, 2);

		$this->set('gallery_id', $gallery_id);
		$this->set('removed', false);
		$this->set('tag_id', $tag_id);
		$this->set('tag', $gallery_tag);
		$this->render('mark_tag_ajax', 'ajax');
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
	function getTagSizes($tag_array, $gallery_id) {
		$final_tags = Array();
		$counter = 0;
		foreach ($tag_array as $current_tag) {
			$current_id = $current_tag['Tag']['id'];
			$tag_count = $this->GalleryTag->findCount("gallery_id = $gallery_id AND tag_id = $current_id");
			$current_tag['GalleryTag']['size'] = $this->getTagSize($tag_count);

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
	* Handles comment reply action
	**/
	function comment_reply($source_id, $comment_level) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$commenter_id = $this->getLoggedInUserID();
		$isLogged = $this->isLoggedIn();
		$source_comment = $this->Gcomment->find("Gcomment.id = $source_id");
		$gallery_id = $source_comment['Gcomment']['gallery_id'];
		$gallery = $this->Gallery->find("Gallery.id = $gallery_id");
		$gallery_name = $gallery['Gallery']['name'];
		$gallery_owner_id = $gallery['User']['id'];
		$comment_owner_id = $source_comment['User']['id'];
		$error = Array();
		
		$gallery_url = "/galleries/view/$gallery_id";
		$gallery_link = "<a href='$gallery_url'>$gallery_name</a>";
		
        if (!empty($this->params['form'])) {
            if ($user_id) {
				$content_name = 'gallery_comment_reply_input_' . $source_id;
                $comment = htmlspecialchars($this->params['form'][$content_name]);

				// SPAM checking
				$possible_spam = false;
				$excessive_commenting = false;
				$days = COMMENT_SPAM_MAX_DAYS;
				$max_comments = COMMENT_SPAM_CLEAR_COMMENTS;
				$time_limit = COMMENT_SPAM_CLEAR_MINUTES;
				$comment_length = strlen($comment);
				$recent_comments_by_user = $this->Gcomment->findAll("Gcomment.user_id =  $commenter_id AND Gcomment.timestamp > now() - interval $time_limit minute AND Gcomment.gallery_id = $gallery_id");
				if(sizeof($recent_comments_by_user)>$max_comments)
				{
				  $excessive_commenting = true;
				}
				$nowhite_comment = ereg_replace("[ \t\n\r\f\v]{1,}", "[ \\t\\n\\r\\f\\v]*", $comment);
				if($comment_length > COMMENT_LENGTH):
				$similar_comments = $this->Gcomment->findAll("Gcomment.content RLIKE '".$nowhite_comment."' AND Gcomment.timestamp > now() - interval $days  day AND Gcomment.user_id = $commenter_id");
				preg_match_all("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $comment, $url_matches);
				for($i=0; $i<count($url_matches[0]); $i++)
				{
				  $url_to_check = $url_matches[0][$i];
				  if(sizeof($this->Gcomment->findAll("Gcomment.content LIKE '%".$url_to_check."%' AND Gcomment.timestamp > now() - interval $days  day AND Gcomment.user_id = $commenter_id"))>1)
				  {
				      $possible_spam = true;
				  }
				}
				if(sizeof($similar_comments)>$max_comments)
				{
				    $possible_spam = true;
				}
				endif;
				if(isInappropriate($comment)) {
					$vis = 'censbyadmin';
					$this->notify('inappropriate_gcomment_reply', $user_id,
								array('gallery_id' => $gallery_id),
								array($comment));
				} else {
					$vis = 'visible';
				}
				
				
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
					$duplicate_record = $this->Gcomment->find("Gcomment.gallery_id = $gallery_id AND Gcomment.user_id = $user_id AND Gcomment.content = '$comment' AND Gcomment.reply_to != -100");
					if($comment_length > COMMENT_LENGTH):
					if (empty($duplicate_record)) {
						$duplicate = false;
					} else {
						$original = $duplicate_record['Gcomment']['timestamp'];
						$today = time(); /* Current unix time */
						$since = $today - strtotime($original);
						if ($since < 60) {
							$duplicate = true;
						} else {
							$duplicate = false;
						}
					}
					else:
					if (empty($duplicate_record)) {
						$duplicate = false;
					} else {
							$duplicate = true;
					}
					endif;
					if ($duplicate) {
					} else {
						$new_reply = array('Gcomment'=>array('id' => null, 'gallery_id'=>$gallery_id, 'user_id'=>$user_id, 'content'=>$comment, 'comment_visibility'=>$vis, 'reply_to' => $source_id));
						$this->Gcomment->save($new_reply);
                        $gcomment_id = $this->Gcomment->getInsertID();
                        $this->Gcomment->deleteCommentsFromMemcache($gallery_id);
						$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.user_id = $commenter_id AND (IgnoredUser.blocker_id = $gallery_owner_id OR IgnoredUser.blocker_id = $comment_owner_id)");
						
						if ($ignore_count == 0 && $vis == 'visible') {
							//user is not replying to his own comment
							if($commenter_id != $comment_owner_id) {
								//comment reply notification to comment_owner
								$this->notify('new_gcomment_reply', $comment_owner_id,
										array('gallery_id' => $gallery_id,
										'from_user_name' => $this->getLoggedInUsername(),
                                        'comment_id' => $gcomment_id
                                        )
									);
							}
							//send notification to gallery owner if gallery owner and comment owner are differnt
							//and comment replier is not the gallery owner
							if($gallery_owner_id != $comment_owner_id && $commenter_id != $gallery_owner_id) {
								//comment notification to gallery_owner
								$this->notify('new_gcomment_reply_to_owner', $gallery_owner_id,
										array('gallery_id' => $gallery_id,
										'from_user_name' => $this->getLoggedInUsername(),
                                        'comment_id' => $gcomment_id
                                        )
									);
							}
						}
					}
				}
			}
		}

		$replies = $this->set_replies($gallery_id, $gallery_owner_id, $source_id, $user_id, $isLogged);
		$this->set_comment_errors(array());
		
		$this->set('replies', $replies);
		$this->set('isThemeOwner', $user_id == $gallery['User']['id']);
		$this->set('theme_id', $gallery_id);
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

        $gallery_id = $this->Gcomment->field('gallery_id', 'Gcomment.id = '. $source_id);
        $this->Gallery->recursive = -1;
        $gallery = $this->Gallery->find('Gallery.id = '.$gallery_id,
                                            'Gallery.user_id');
		$replies = $this->set_replies($gallery_id, $gallery['Gallery']['user_id'],
                                                $source_id, $user_id, $isLogged);

		$this->set('replies', $replies);
		$this->set('isThemeOwner', $user_id == $gallery['Gallery']['user_id']);
		$this->set('theme_id', $gallery_id);
		$this->set('comment_level', $comment_level + 1);
        $this->set('isLogged', $isLogged);
		$this->render('comment_reply_ajax', 'ajax');
        
        /*$this->set('gallery_id', $gallery_id);
		$this->set('comments', $final_comments);
		$this->set('comment_level', $comment_level + 1);
		$this->set('isLogged', $isLogged);
		$this->render('comment_reply_ajax', 'ajax');*/
	}

	/**
	* Expands the description of a project in the explorer view
	**/
	function expandDescription($gallery_id, $secondary = null) {
		$this->autoRender = false;
		$this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();
		$user_id = $this->getLoggedInUserID();	
		$isLogged = $this->isLoggedIn();
		
		$gallery = $this->finalize_gallery($gallery);
		$this->set('gallery', $gallery);
		$this->render('expand_description_ajax', 'ajax');
	}
	
	
	
	/**
	* Returns all comments relevant to logged in user viewing a gallery
	**/
	function set_comments($gallery_id, $owner_id, $user_id, $isLoggedIn, $comment_id = null) {
		$comment_data = false;
        
        //no comment id is set that means we are not fetching a specific comment thread
        if(empty($comment_id)) {
            //do pagination stuffs
            $this->PaginationSecondary->show = GALLERY_COMMENT_PAGE_LIMIT;
            $this->modelClass = 'Gcomment';
            $options = array('sortBy' => 'timestamp', 'sortByClass' => 'Gcomment',
						'direction' => 'DESC', 'url' => '/galleries/renderComments/' . $gallery_id);
            list($order, $limit, $page) = $this->PaginationSecondary->init( 'gallery_id = '
                . $gallery_id . ' AND comment_visibility = "visible" AND reply_to = -100',
                array(), $options);

            //check memcache
            $this->Gcomment->mc_connect();
            $mc_key = $gallery_id.'_'.$isLoggedIn.'_'.$page;
            $num_cache_pages = GCOMMENT_CACHE_NUMPAGE; //we will store only first few pages

            if($page <= $num_cache_pages) {
                $comment_data = $this->Gcomment->mc_get('gcomments', $mc_key);
            }
            
            $comment_condition = ' AND reply_to = -100';
        }
        //comment id is set, we are fetching a specific comment thread
        else {
            $comment_condition = ' AND Gcomment.id = ' . $comment_id;
            $order = null;
            $limit = 1;
            $page = null;
        }

        //not yet cached
        if($comment_data === false) {
            $this->Gcomment->unbindModel( array('belongsTo' => array('Gallery')) );
            $comments = $this->Gcomment->findAll('gallery_id = ' . $gallery_id
                . ' AND comment_visibility = "visible"' . $comment_condition,
                null, $order, $limit, $page);

            $commenter_ids = array();
            $comment_ids = array();

            foreach ($comments as $key => $comment) {
                $commenter_ids[] = $comment['Gcomment']['user_id'];
                $comment_ids[]   = $comment['Gcomment']['id'];

                $comment['Gcomment']['content'] = $this->set_comment_content($comment['Gcomment']['content']);

                $comment['Gcomment']['replies'] = $this->Gcomment->findCount('gallery_id = '
                    . $gallery_id . ' AND reply_to = '. $comment['Gcomment']['id']);

                $comment['Gcomment']['replylist'] = array();
                if($comment['Gcomment']['replies'] > 0) {
                    $comment['Gcomment']['replylist'] = $this->set_replies($gallery_id,
                        $owner_id, $comment['Gcomment']['id'], $user_id, $isLoggedIn, NUM_COMMENT_REPLY);
                }

                //replace the comment in $comments list
                $comments[$key] = $comment;
            }

            $commenter_ids  = $this->IgnoredUser->createString($commenter_ids);
            $comment_ids    = $this->IgnoredUser->createString($comment_ids);

            $this->IgnoredUser->recursive = -1;
            $ignored_commenters = $this->IgnoredUser->find('list',
                array('conditions' =>
                'blocker_id = '. $owner_id . ' AND user_id IN ' . $commenter_ids,
                'fields' => 'user_id'));

            $comment_data = array();
            $comment_data['comments'] = $comments;
            $comment_data['ignored_commenters'] = $ignored_commenters;
            $comment_data['ignored_comments'] = array();
            $comment_data['commenter_ids'] = $commenter_ids;
            $comment_data['comment_ids'] = $comment_ids;
            
            //we will store the data of first few pages if comment id is not set
            if(empty($comment_id) && $page <= $num_cache_pages) {
                $this->Gcomment->mc_set('gcomments', $comment_data, $mc_key);
            }
        }

        if(empty($comment_id)) {
            //close memcache connection
            $this->Gcomment->mc_close();
        }
        
        //if the user is logged in
        if($isLoggedIn) {
            $this->IgnoredUser->recursive = -1;
            $user_ignored_commenters = $this->IgnoredUser->find('list',
                    array('conditions' =>
                    'blocker_id = '. $user_id. ' AND user_id IN ' . $comment_data['commenter_ids'],
                    'fields' => 'user_id'));

            $this->Mgcomment->recursive = -1;
            $user_ignored_comments = $this->Mgcomment->find('list',
                array('conditions' =>
                'user_id = ' . $user_id . ' AND comment_id IN ' . $comment_data['comment_ids'],
                'fields' => 'comment_id'));

            $comment_data['ignored_commenters'] = $comment_data['ignored_commenters'] + $user_ignored_commenters;
            $comment_data['ignored_comments']   = $comment_data['ignored_comments'] + $user_ignored_comments;
        }

        $comment_data['single_thread'] = !empty($comment_id);
		return $comment_data;
	}
	
	/**
	* Helper for setting comment content
	**/
	function set_comment_content($initial_content) {
		$comment_content = $initial_content;
		$comment_content  = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/projects/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to project', true) . ")</a>",  $comment_content);
		$comment_content  = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/forums/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to forums', true) . ")</a>",  $comment_content);
		$comment_content = ereg_replace("([[:alpha:]]+://)?scratch.mit.edu(/galleries/([^<>[:space:]]+[[:alnum:]/]))", "<a href=\"\\2\">(" . ___('link to gallery', true) . ")</a>",  $comment_content);
		
		return $comment_content;
	}
	
	/**
	*
	**/
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
	* Returns all tags relevant to logged in user viewing a gallery
	**/
	function set_tags($gallery_id, $user_id, $isLogged) {
		$gallery_tags = $this->GalleryTag->findAll("gallery_id = $gallery_id");

		$final_tags = Array();
		$all_tags = Array();
		$counter = 0;
		foreach ($gallery_tags as $current_tag) {
			$tag_id = $current_tag['GalleryTag']['tag_id'];
			if ($isLogged) {
				$tagged_record = $this->GalleryTag->findAll("GalleryTag.user_id = $user_id AND GalleryTag.gallery_id = $gallery_id AND GalleryTag.tag_id = $tag_id");
				$flagged_record = $this->TagFlag->findAll("TagFlag.user_id = $user_id AND tag_id = $tag_id");
			} else {
				$tagged_record = Array();
				$flagged_record = Array();
			}
			if (empty($flagged_record)) {
				$flagged = false;
			} else {
				$flagged = true;
			}

			if (empty($tagged_record)) {
				$tagged = false;
			} else {
				$tagged = true;
			}

			$current_tag['GalleryTag']['flagged'] = $flagged;
			$current_tag['GalleryTag']['tagged'] = $tagged;

			$current_tag_id = $current_tag['Tag']['id'];
			if (in_array($current_tag_id, $all_tags)) {
			} else {
				array_push($all_tags, $current_tag_id);
				$final_tags[$counter] = $current_tag;
				$counter++;
			}
		}
		//sets tag sizes
		$final_tags = $this->getTagSizes($final_tags, $gallery_id);
		return $final_tags;
	}
	
	/**
	* Returns all replies relevant to logged in user viewing a gallery
	**/
	function set_replies($gallery_id, $owner_id, $parent_id, $user_id, $isLoggedIn, $limit = 0) {
		$this->Gcomment->unbindModel( array('belongsTo' => array('Gallery')) );
        $comments = $this->Gcomment->findAll('gallery_id = ' . $gallery_id
            . ' AND comment_visibility = "visible" AND reply_to = ' . $parent_id,
            null, 'Gcomment.timestamp DESC', $limit);

        //set comments info
        $commenter_ids = array();
        $comment_ids = array();

        foreach ($comments as $key => $comment) {
            $commenter_ids[] = $comment['Gcomment']['user_id'];
            $comment_ids[]   = $comment['Gcomment']['id'];

            $comment['Gcomment']['content'] = $this->set_comment_content($comment['Gcomment']['content']);

            $comment['Gcomment']['replies'] = $this->Gcomment->findCount('gallery_id = '
                . $gallery_id . ' AND reply_to = '. $comment['Gcomment']['id']);

            //replace the comment in $comments list
            $comments[$key] = $comment;
        }

        $commenter_ids  = $this->IgnoredUser->createString($commenter_ids);
        $comment_ids    = $this->IgnoredUser->createString($comment_ids);

        $this->IgnoredUser->recursive = -1;
        $ignored_commenters = $this->IgnoredUser->find('list',
            array('conditions' =>
            'blocker_id = '. $owner_id . ' AND user_id IN ' . $commenter_ids,
            'fields' => 'user_id'));

        $comment_data = array();
        $comment_data['comments'] = $comments;
        $comment_data['ignored_commenters'] = $ignored_commenters;
        $comment_data['ignored_comments'] = array();
        $comment_data['commenter_ids'] = $commenter_ids;
        $comment_data['comment_ids'] = $comment_ids;

        //if the user is logged in
        if($isLoggedIn) {
            $this->IgnoredUser->recursive = -1;
            $user_ignored_commenters = $this->IgnoredUser->find('list',
                    array('conditions' =>
                    'blocker_id = '. $user_id. ' AND user_id IN ' . $comment_data['commenter_ids'],
                    'fields' => 'user_id'));

            $this->Mgcomment->recursive = -1;
            $user_ignored_comments = $this->Mgcomment->find('list',
                array('conditions' =>
                'user_id = ' . $user_id . ' AND comment_id IN ' . $comment_data['comment_ids'],
                'fields' => 'comment_id'));

            $comment_data['ignored_commenters'] = $comment_data['ignored_commenters'] + $user_ignored_commenters;
            $comment_data['ignored_comments']   = $comment_data['ignored_comments'] + $user_ignored_comments;
        }

		return $comment_data;
	}
	
	/**
	* Helper for trimming gallery records
	**/
	function set_galleries($galleries) {
		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		$return_galleries = Array();
		
		foreach ($galleries as $gallery) {
			$temp_gallery = $gallery;
			$current_user_id = $temp_gallery['Gallery']['user_id'];
			$temp_gallery['Gallery']['ignored'] = false;
			if ($isLogged) {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $current_user_id");
				if ($ignore_count > 0) {
					$temp_gallery['Gallery']['ignored'] = true;
				} else {
					$temp_gallery['Gallery']['ignored'] = false;
				}
			}
			array_push($return_galleries, $temp_gallery);
		}
		return $return_galleries;
	}
	
	/**
	* Helper for setting the projects of a gallery
	**/ 
	function set_gallery_projects_full($gallery_id, $option=null, $criteria = null) {
		$this->modelClass = "GalleryProject";
		$total_projects = $this->GalleryProject->findCount("gallery_id = $gallery_id");
		$this->Gallery->id = $gallery_id;
		$current_gallery = $this->Gallery->read();
		$owner_id =$current_gallery['Gallery']['user_id'];
		$content_status = $this->getContentStatus();

		$limit = 15;
		$this->Pagination->show = $limit;
		
		$this->GalleryProject->bindProject();
		$this->Project->bindUser();

		//listing ignored user by gallery owner.
		$ignored_user_array =array();
		$ignore_user_list = $this->IgnoredUser->findAll("IgnoredUser.blocker_id = $owner_id",'user_id');
		foreach($ignore_user_list as $ignore_user)
		array_push($ignored_user_array,$ignore_user['IgnoredUser']['user_id']);
		$ignored_list = implode(',',$ignored_user_array);
		if(!empty($ignored_list)){
			if ($content_status == 'safe') {
				$conditions = "gallery_id = $gallery_id AND Gallery.visibility = 'visible' AND Project.status = 'safe' AND Project.user_id not in (".$ignored_list.") ";
			} else {
				$conditions = "gallery_id = $gallery_id AND Gallery.visibility = 'visible' AND Project.user_id not in (".$ignored_list.") ";
			}
		}
		else{
		if ($content_status == 'safe') {
				$conditions = "gallery_id = $gallery_id  AND Gallery.visibility = 'visible' AND Project.status = 'safe' ";
			} else {
				$conditions = "gallery_id = $gallery_id  AND Gallery.visibility = 'visible'";
			}
		
		}
		if ($option == "creator") {
			$options = Array("sortBy"=>"user_id", "sortByClass" => "Project",
							"direction"=> "DESC", "url"=>"/galleries/renderProjects/" . $gallery_id . "/creator");
		} elseif ($option == "title") {
			$options = Array("sortBy"=>"name", "sortByClass" => "Project",
							"direction"=> "ASC", "url"=>"/galleries/renderProjects/" . $gallery_id . "/title");
		} elseif($option == "created") {
			$options = Array("sortBy"=>"created", "sortByClass" => "Project",
							"direction"=> "DESC", "url"=>"/galleries/renderProjects/" . $gallery_id . "/created");
		} elseif ($option == "added") {
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "GalleryProject",
							"direction"=> "DESC", "url"=>"/galleries/renderProjects/" . $gallery_id . "/added");
		} else {
			$options = Array("sortBy"=>"timestamp", "sortByClass" => "GalleryProject",
								"direction"=> "DESC", "url"=>"/galleries/renderProjects/" . $gallery_id . "/added");
		}
		if(!empty($ignored_list)){
		list($order,$limit,$page) = $this->Pagination->init("gallery_id = $gallery_id  AND Gallery.visibility = 'visible' AND Project.user_id not in (".$ignored_list.") ", Array(), $options);
		}
		else
		list($order,$limit,$page) = $this->Pagination->init("gallery_id = $gallery_id  AND Gallery.visibility = 'visible'", Array(), $options);
		$gallery_projects = $this->GalleryProject->findAll($conditions, null, $order, $limit, $page, 3);
		$gallery_projects = $this->set_gallery_projects($gallery_projects, $gallery_id);
		$data = $gallery_projects;
		
		$this->set('theme_projects', $gallery_projects);
		
		return $page;
	}
	
	function set_gallery_projects($gallery_projects, $gallery_id) {
		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		$gallery = $this->Gallery->find("Gallery.id = $gallery_id");
		$creator_id = $gallery['Gallery']['user_id'];
		
		$return_projects = Array();
		foreach ($gallery_projects as $current_project) {
			$temp_project = $current_project;
			$owner_id = $current_project['Project']['user_id'];
			$temp_project['GalleryProject']['ignored'] = false;
			if ($isLogged) {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $owner_id");
				$ignore_count = $ignore_count + $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $owner_id");
				if ($ignore_count > 0) {
					$temp_project['GalleryProject']['ignored'] = true;
				} else {
					$temp_project['GalleryProject']['ignored'] = false;
				}
			} else {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $creator_id AND IgnoredUser.user_id = $owner_id");
				if ($ignore_count > 0) {
					$temp_project['GalleryProject']['ignored'] = true;
				} else {
					$temp_project['GalleryProject']['ignored'] = false;
				}
			}
			array_push($return_projects, $temp_project);
		}
		return $return_projects;
	}
	
	function all_comment($gallery_id=null){
			$count =1;
			$comment_index_array = array();
			$comments = $this->Gcomment->findAll("gallery_id = $gallery_id AND Gcomment.comment_visibility = 'visible' AND reply_to = -100",'id,reply_to','Gcomment.timestamp DESC');
			
			foreach($comments as $key=>$value){
			
				$index = $key;
				$comment_id = $value['Gcomment']['id'];
				$comment_index_array[$count] =$comment_id;
				$count++;
			}
			return $comment_index_array;
	}
	function comment_index($c_id=null){
			
			 $comment = $this->Gcomment->find("Gcomment.id=$c_id",'id,reply_to');
			 if($comment['Gcomment']['reply_to']==-100){
			  return $comment;
			  }
			  else
			  {
			  return ($this->comment_index($comment['Gcomment']['reply_to']));
			  }
			
	}
}
?>
