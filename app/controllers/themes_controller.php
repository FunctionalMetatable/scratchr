<?php
Class ThemesController extends AppController {

	/*----------------------------------------*
	Notice: "Gallery" is used as an alias for
			"Theme" within the views
	*-----------------------------------------*/

    var $name = "Themes";
    var $uses = array("Project", "ClubbedTheme", "FeaturedTheme", "ThemeProject", "Theme", "ThemeMembership","Tcomment","User","RelationshipType","Relationship","ThemeRequest", "Notification"); //apparently order matters for associative finds
    var $helpers = array('Pagination','Ajax','Javascript');
    var $components = array('Pagination', 'RequestHandler', 'FileUploader');


	/**
     * Renders theme controller error page
	 * Overrides AppController::__err()
     */
    function __err() {
        $this->render('terror');
        die;
    }


    /**
     * Ajax-updates the title for the given project
     * @param string $urlname => user url
     * @param int $pid => project id
     */
    function name($theme_id) {
	    $this->exitOnInvalidArgCount(1);
		if (!$this->RequestHandler->isAjax())
			exit;

		$session_uid = $this->getLoggedInUserID();
		if (!$session_uid)
			exit;

        $this->autoRender=false;

        $this->Theme->id=$theme_id;
        $theme = $this->Theme->read();

		if (empty($theme))
			exit;

		if (!$this->isAdmin())
			if ($theme['User']['id'] !== $session_uid)
				exit;

		$inputText = (isset($this->params['form']['name'])) ? $this->params['form']['name'] : null;
		if ( $inputText ) {
			$newtitle = htmlspecialchars( $inputText );
			if(isInappropriate($newtitle))
			{
			        $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$this->notify($user_id, 'We remind you to use appropriate language for all ages, please read the <a href="/terms">Community Guidelines</a>', false, false);
			}
			else
			{
				if ($this->Theme->saveField('name',$newtitle)) {
				   $this->set('gtitle', $newtitle);
				   $this->render('themetitle_ajax', 'ajax');
				   return;
				}
			}
		}
        $this->set('gtitle', $theme['Theme']['name']);
        $this->render('themetitle_ajax','ajax');
        return;
    }


	/**
     * Update description action the given project
     * @param string $urlname => user url
     * @parm int $pid => project id
     */
    function describe($theme_id) {
        $this->exitOnInvalidArgCount(1);
		if (!$this->RequestHandler->isAjax())
			exit;

		$session_uid = $this->getLoggedInUserID();
		if (!$session_uid)
			exit;

        $this->autoRender=false;

        $this->Theme->id=$theme_id;
        $theme = $this->Theme->read();

		if (empty($theme))
			exit;

		if (!$this->isAdmin())
			if ($theme['User']['id'] !== $session_uid)
				exit;

        if (isset($this->params['form']['description'])) {
            $newdesc = htmlspecialchars($this->params['form']['description']);
	    if(isInappropriate($newdesc))
	    {
		$user_record = $this->Session->read('User');
		$user_id = $user_record['id'];
		$this->notify($user_id, 'We remind you to use appropriate language for all ages, please read the <a href="/terms">Community Guidelines</a>', false, false);
	    }
	    else
	    {
		if ($this->Theme->saveField('description', $newdesc)) {
                   $this->set('gdesc', $newdesc);
                   $this->render('themedescription_ajax','ajax');
                   return;
            	}
	    }
        }
        $this->set('gdesc',$theme['Theme']['description']);
        $this->render('themedescription_ajax','ajax');
        return;
    }


	/**
	 * Admin Feature a theme
	 */
	function feature() {
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || !$this->isAdmin() || empty($this->params['form']))
			exit;

		$theme_id = $this->params['form']['theme-id'];
		$this->FeaturedTheme->id=null;

		if ($this->FeaturedTheme->save(Array('FeaturedTheme'=>Array('theme_id'=>$theme_id))))
		{
			$this->set('theme_id', $theme_id);
			$this->set("isFeatured", true);
			$this->set("isClubbed", $this->params['form']['isClubbed']);
			$this->render('admin_actions', 'ajax');
		}
		return;
	}


	/**
	 * Admin deFeature a theme
	 */
	function defeature() {
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || !$this->isAdmin() || empty($this->params['form']))
			exit;

		$theme_id = $this->params['form']['theme-id'];
		$featured_theme = $this->FeaturedTheme->find("theme_id = $theme_id", NULL, "FeaturedTheme.id DESC");

		if (!empty($featured_theme))
		{
			if ($this->FeaturedTheme->del($featured_theme["FeaturedTheme"]["id"]))
			{
				$this->set('theme_id', $theme_id);
				$this->set("isFeatured", false);
				$this->set("isClubbed", $this->params['form']['isClubbed']);
				$this->render('admin_actions', 'ajax');
			}
		}
		exit;
	}


	function club()
	{
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || !$this->isAdmin() || empty($this->params['form']))
			exit;

		$theme_id = $this->params['form']['theme-id'];
		$this->ClubbedTheme->id=null;

		if ($this->ClubbedTheme->save(Array('ClubbedTheme'=>Array('theme_id'=>$theme_id))))
		{
			$this->set('theme_id', $theme_id);
			$this->set("isClubbed", true);
			$this->set("isFeatured", $this->params['form']['isFeatured']);
			$this->render('admin_actions', 'ajax');
		}
		exit;
	}


	function declub()
	{
		$user_id = $this->getLoggedInUserID();
		if (!$user_id || !$this->isAdmin() || empty($this->params['form']))
			exit;

		$theme_id = $this->params['form']['theme-id'];
		$clubbed_theme = $this->ClubbedTheme->find("theme_id = $theme_id", NULL, "ClubbedTheme.id DESC");

		if (!empty($clubbed_theme))
		{
			if ($this->ClubbedTheme->del($clubbed_theme["ClubbedTheme"]["id"]))
			{
				$this->set('theme_id', $theme_id);
				$this->set("isClubbed", false);
				$this->set("isFeatured", $this->params['form']['isFeatured']);
				$this->render('admin_actions', 'ajax');
			}
		}
		exit;
	}


	/**
	 * For Admin
	 */
	function addmembers()
	{
		return false;
		$theme_id = $this->params['url']['gid'];
		$session_UID = $this->getLoggedInUserID();
		$isAdmin = $this->isAdmin();

		if (!$session_UID)
			$this->cakeError('error404');

		$this->Theme->id = $theme_id;
		$theme = $this->Theme->read();
		if (empty($theme) || !$isAdmin)
			$this->cakeError('error404');

		if (empty($this->params['form']))
		{
			$users_not_member = $this->User->query("SELECT * from users as User WHERE (User.id != $session_UID) AND (NOT EXISTS (SELECT * from theme_memberships WHERE (theme_memberships.user_id = User.id AND theme_memberships.theme_id = $theme_id)))");
			$this->set('users', $users_not_member);
			$this->set('theme_id', $theme_id);
			$this->set('theme_name', $theme['Theme']['name']);
		}
		else
		{
			foreach (array_keys($this->params['form']) as $uid)
			{
				if ($uid !== $session_UID)
				{
					// todo: check not already a member
					$this->ThemeMembership->create();
					$this->ThemeMembership->save(array("user_id"=>$uid, "theme_id"=>$theme_id));
				}
			}

			$this->setFlash("Users added", FLASH_NOTICE_KEY);
			$this->redirect("/galleries/addmembers?gid=".$theme_id);
		}
	}


	/**
	 * removers users from theme
	 */
	function delmembers()
	{
		return false;
		$theme_id = $this->params['url']['gid'];
		$session_UID = $this->getLoggedInUserID();
		$isAdmin = $this->isAdmin();

		if (!$session_UID)
			$this->cakeError('error404');

		$this->Theme->id = $theme_id;
		$theme = $this->Theme->read();
		if (empty($theme) || (!$isAdmin && $theme['Theme']['user_id'] !== $session_UID))
			$this->cakeError('error404');

		if (empty($this->params['form']))
		{
			$members_of_theme = $this->ThemeMembership->findAll("theme_id = $theme_id AND user_id != $session_UID AND user_id != ".$theme['Theme']['user_id']);
			$this->set('users', $members_of_theme);
			$this->set('theme_id', $theme_id);
			$this->set('theme_name', $theme['Theme']['name']);
		}
		else
		{
			foreach (array_keys($this->params['form']) as $uid)
			{
				if ($uid !== $session_UID && $uid !== $theme['Theme']['user_id'])
				{
					$tm = $this->ThemeMembership->find("theme_id = $theme_id AND user_id = $uid");
					$this->ThemeMembership->del($tm['ThemeMembership']['id']);
				}
			}

			$this->setFlash("Members removed", FLASH_NOTICE_KEY);
			$this->redirect("/galleries/delmembers?gid=".$theme_id);
		}
	}


	/**
	 * invites users to join theme
	 */
	function invite()
	{
		$theme_id = $this->params['url']['gid'];
		$session_UID = $this->getLoggedInUserID();

		if (!$session_UID)
			$this->cakeError('error404');

		$this->Theme->id = $theme_id;
		$theme = $this->Theme->read();
		if (empty($theme) || $theme['Theme']['user_id'] !== $session_UID)
			$this->cakeError('error404');

		if (empty($this->params['form']))
		{
			// todo: modify to join user table first then relationship
			// query: retrieves all friends of current session user that
			// are not member of $theme_id and do not already have a
			// pending gallery request to join $theme_id
			$friends_not_member = $this->Relationship->query(
			"SELECT DISTINCT `User`.`id`, `User`.`username`,
			`User`.`firstname`, `User`.`lastname`, `User`.`city`,
			`User`.`state`, `User`.`country`, `User`.`urlname`,
			`User`.`role`, `User`.`email`, `User`.`password`,
			`User`.`buddyicon`, `User`.`created`, `User`.`timestamp`
			FROM `relationships` AS `Relationship`
			LEFT JOIN `users` AS User
			ON `Relationship`.`friend_id` = `User`.`id`
			LEFT JOIN `relationship_types` AS `RelationshipType`
			ON `Relationship`.`relationship_type_id` = `RelationshipType`.`id`
			LEFT JOIN `theme_memberships` AS `Member`
			ON `Relationship`.`friend_id` = `Member`.`user_id`
			WHERE ( Relationship.user_id = ".$session_UID.") AND ( RelationshipType.name = 'friend')
					AND ( NOT EXISTS (SELECT * FROM theme_memberships
						WHERE theme_memberships.theme_id = ".$theme_id." AND theme_memberships.user_id = User.id))
					AND ( NOT EXISTS (SELECT * FROM theme_requests
						WHERE theme_requests.user_id = ".$session_UID." AND theme_requests.to_id = User.id AND theme_requests.theme_id = ".$theme_id."))
			ORDER BY User.username ASC");

			$this->set('users', $friends_not_member);
			$this->set('theme_id', $theme_id);
			$this->set('theme_name', $theme['Theme']['name']);
		}
		else
		{
			foreach (array_keys($this->params['form']) as $uid)
			{
				// TODO: make sure $uid is actually a friend
				$this->__saveGalleryInviteRequest($uid, $theme_id, $session_UID);
			}

			$this->setFlash("Friends Invited", FLASH_NOTICE_KEY);
			$this->redirect("/galleries/invite?gid=".$theme_id);
		}
	}


	/**
	 * Ajax invites user to join themes from myscratchr page
	 */
	function inviteuser()
	{
		$session_UID = $this->getLoggedInUserID();

		if (!$session_UID)
			$this->cakeError('error404');

		$uid = $this->params['url']['uid'];
		$user = $this->User->find("id = $uid");

		if (empty($user))
			$this->cakeError('error404');

		if (!empty($this->params["form"]))
		{
			foreach (array_keys($this->params['form']) as $theme_id)
			{
				$this->Theme->id = $theme_id;
				$theme_owner_id = $this->Theme->field("user_id");

				if ($theme_owner_id !== $session_UID)
					continue;

				$this->__saveGalleryInviteRequest($uid, $theme_id, $session_UID);
			}
			$this->setFlash("User Invited", FLASH_NOTICE_KEY);
		}
		$this->redirect("/users/".$user['User']['username']);
	}


	function __saveGalleryInviteRequest($uid, $theme_id, $session_UID)
	{
		if (!$this->ThemeMembership->hasAny(Array("user_id"=>$uid, "theme_id"=>$theme_id))
			&& !$this->ThemeRequest->hasAny(Array("user_id"=>$session_UID, "to_id"=>$uid, "theme_id"=>$theme_id)))
		{
			$this->ThemeRequest->id=null;
			$this->ThemeRequest->save(Array("user_id"=>$session_UID, "to_id"=>$uid, "theme_id"=>$theme_id, "status"=>"pending"));
		}
	}



	/**
	 * Creates a new theme
	 * @param string $themename => a new themename
	 */
	function create($theme_name=null) {
		$UID = $this->getLoggedInUserID();
		if (!$UID)
			$this->cakeError('error404');

		if (!empty($this->params["form"])) {
			$name = $this->params["form"]["theme_name"];
			if(isInappropriate($name))
			{
				$name = "Untitled";
			        $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$this->notify($user_id, 'We remind you to use language appropriate for all ages when choosing the title of a gallery. Please read the <a href="/terms">Community Guidelines</a>', false);
			}
			$description = $this->params["form"]["theme_description"];
			if(isInappropriate($description))
			{
				$description = "";
			        $user_record = $this->Session->read('User');
				$user_id = $user_record['id'];
				$this->notify($user_id, 'We remind you to use language appropriate for all ages when choosing the description of a gallery. Please read the <a href="/terms">Community Guidelines</a>', false);
			}
			$visibility = 1; #intval($this->params["form"]["visibility"]);
			// TODO: assert 'visibility' is within range, 1-3

			if ($this->Theme->save(Array("Theme"=>Array("name"=>$name, "description"=>$description, "user_id"=>$UID)))) {

				$theme_id = $this->Theme->getLastInsertID();
				if (!$this->ThemeMembership->save(Array("ThemeMembership"=>Array("user_id"=>$UID, "theme_id"=>$theme_id, "type"=>"owner")))) {
					$this->setFlash("Error saving theme info", FLASH_ERROR_KEY);
					$this->render();
					exit;
				}

				if (!empty($this->params["form"]["icon"]["name"])) {
					$icon_array = $this->params["form"]["icon"];
					$theme_icon_file = WWW_ROOT . getThemeIcon($theme_id, false, DS);
					mkdirR(dirname($theme_icon_file) . DS);
					$error = $this->FileUploader->handleFileUpload($icon_array, $theme_icon_file);
					if ($error)
					{
						$this->setFlash($error, FLASH_ERROR_KEY);
						$this->render();
						exit;
					}
				}
				$this->redirect('/galleries/'.$theme_id);
				exit;
			}
			$this->setFlash("Error saving information", FLASH_ERROR_KEY);
		}
	}


	/**
	 * Deletes the theme referred to by theme_id
	 * @param int $theme_id => theme identifier
	 */
	function delete() {
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (!isset($this->params['form']['theme-id']))
			$this->cakeError('error404');

		$theme_id = $this->params['form']['theme-id'];

		$theme = $this->Theme->read('user_id',intval($theme_id));
		if (empty($theme['Theme']['user_id']))
			$this->cakeError('error404');

		if ($this->isAdmin() || $theme['Theme']['user_id'] == $user_id) {
			if ($this->Theme->remove($theme_id)) {
				$this->setFlash("Gallery deleted", FLASH_NOTICE_KEY);
				$this->redirect('/galleries');
			}
		}
		exit;
	}


	/**
	 * Subscribes user to theme given by theme_id
	 * Handles both subscribing through myscratchr page
	 * as well as through the themepage
	 */
	function subscribe() {
		$UID = $this->getLoggedInUserID();

		if (empty($this->params['form']['theme-id'])
			|| !$this->RequestHandler->isAjax()
			|| !$UID)
			exit;

		$this->ThemeMembership->id = null;
		$theme_id = $this->params['form']['theme-id'];

		if ($this->ThemeMembership->save(Array("ThemeMembership"=>Array("user_id"=>$UID, "theme_id"=>$theme_id))))
		{
			$this->set('theme_id', $theme_id);
			$this->set('isThemeMember', true);
			$this->render('subscribe_action', 'ajax');
		}
		exit;
	}


	/**
	 * UnSubscribes user from theme given by theme_id
	 * Handles both unsubscribing through myscratchr page
	 * as well as through the themepage
	 */
	function unsubscribe() {
		if (!$this->RequestHandler->isAjax())
			exit;

		$session_UID = $this->getLoggedInUserID();
		if (!$session_UID)
			exit;

		$user_id = null;
		$theme_id = null;

		$from_myscratchrpage = empty($this->params['form']);

		if ($from_myscratchrpage)
		{
			$theme_id = $this->params['url']['theme_id'];
			$user_id = $this->params['url']['user_id'];

			if (!$this->isAdmin())
				if ($session_UID !== $user_id)
					exit;
		}
		else
		{
			$theme_id = $this->params['form']['theme-id'];
			$user_id = $session_UID;
		}

		$this->ThemeMembership->bindTheme();
		$member_data = $this->ThemeMembership->find("theme_id = ".$theme_id." AND ThemeMembership.user_id = ".$user_id, null, null, 2);

		if (!empty($member_data))
		{
			if ($member_data['Theme']['user_id'] === $user_id)
				echo "You own this theme. <br> You must delete theme";
			else
			{
				$this->ThemeMembership->del($member_data['ThemeMembership']['id']);
				if (!$from_myscratchrpage)
				{
					$this->set('theme_id', $theme_id);
					$this->set('isThemeMember', false);
					$this->render('subscribe_action', 'ajax');
				}
			}
		}
		exit;
	}


	/**
	 * DEPRECATED
	 * Submits specified project to theme
	 * @param int $pid => project id
	 */
	 function submit($pid) {

	 	$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (empty($this->params["form"]))
		{
			$this->set('pid', $pid);
			$this->User->bindMyThemes();
			$this->set('myThemes', $this->ThemeMembership->query("
				SELECT DISTINCT Theme.id, Theme.name FROM theme_memberships
				JOIN themes AS Theme ON Theme.id = theme_memberships.theme_id AND theme_memberships.user_id = ".$user_id."
				 WHERE NOT EXISTS (SELECT * FROM theme_projects
                    WHERE theme_projects.project_id = ".$pid." AND theme_projects.theme_id = theme_memberships.theme_id)
				"));
			$this->render('submit_project','ajax');

		} else {

			$themes = $this->params["form"];
			foreach ($themes as $key => $value)
			{
				$theme_id = $key;
				$this->Theme->bindHABTMProject("Project.id = $pid");
				$theme_projects = $this->Theme->find("Theme.id = $theme_id");
				if (empty($theme_projects['Project'])) {
					$this->ThemeProject->id=null;
					$this->ThemeProject->save(Array('ThemeProject'=>Array('theme_id'=>$theme_id, 'project_id'=>$pid)));
				}
			}
			$this->setFlash("Project added to theme", FLASH_NOTICE_KEY);
			$this->redirect('/users/' . $this->getLoggedInUrlname());
		}
	 }


	/**
	 * Submits multiple projects to multiple themes
	 * @param int $pid => project id
	 */
	 function submitprojects($user_id) {
		$this->autoRender = false;
	 	$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
			exit;

		if (!empty($this->params["form"]))
		{
			$isAdmin = $this->isAdmin();

			$this->User->id = $user_id;
			$user_data = $this->User->read();
			if (empty($user_data))
				exit;

			// retrieve project ids
			$selected_themes = $this->params["form"];
			$cookie_subname = "projectselections";

			$total_projects = -1;

			// todo later: sanitize data
			foreach ($_COOKIE as $cookie => $value)
			{
				$cookie_array = explode("_", $cookie);
				if (!empty($cookie_array[1]) && $cookie_array[1] === $cookie_subname)
				{
					$pid = $cookie_array[2];
					$this->Project->bindUser();
					$this->Project->id = $pid;
					$project = $this->Project->read();
					if (!empty($project['Project']))
					{
						if (!$isAdmin)
							if ($project['User']['id'] !== $session_user_id)
								exit;

						$pid = $project['Project']['id'];

						// save to each theme
						foreach ($selected_themes as $key => $value)
						{
							$theme_id = $key;
							$theme_project = $this->ThemeProject->find("theme_id = ".$theme_id." AND project_id = ".$pid);

							if($total_projects==-1)
							{
								$theme = $this->Theme->find('Theme.id = '.$theme_id);
								$total_projects = $theme['Theme']['total_projects'];
							}
							else
							{
								$theme = $this->Theme->find('Theme.id = '.$theme_id.' AND total_projects = '.$total_projects);
							}
							$theme['Theme']['total_projects']++;
							$total_projects++;

							$this->Theme->id = $theme_id;
							$this->Theme->save($theme);

							if (empty($theme_project['ThemeProject']))
							{
								$this->ThemeProject->id=null;
								$this->ThemeProject->save(Array('ThemeProject'=>Array('theme_id'=>$theme_id, 'project_id'=>$pid)));
							}
						}
					}
				}
			}
			$this->setFlash("Projects added to themes", FLASH_NOTICE_KEY);
			$this->redirect('/users/' . $user_data['User']['urlname']);
		}
		exit;
	 }



	/**
	 * Removes multiple projects from multiple themes
	 * @param int $pid => project id
	 */
	function removeprojects($user_id) {
		$this->autoRender = false;
	 	$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
			exit;

		if (!empty($this->params["form"]))
		{
			$isAdmin = $this->isAdmin();

			$this->User->id = $user_id;
			$user_data = $this->User->read();
			if (empty($user_data))
				exit;

			// retrieve project ids
			$selected_themes = $this->params["form"];
			$cookie_subname = "projectselections";

			$total_projects = -1;

			// todo later: sanitize data
			foreach ($_COOKIE as $cookie => $value)
			{
				$cookie_array = explode("_", $cookie);
				if (!empty($cookie_array[1]) && $cookie_array[1] === $cookie_subname)
				{
					$pid = $cookie_array[2];
					$this->Project->bindUser();
					$this->Project->id = $pid;
					$project = $this->Project->read();
					if (!empty($project['Project']))
					{
						if (!$isAdmin)
							if ($project['User']['id'] !== $session_user_id)
								exit;

						$pid = $project['Project']['id'];

						// save to each theme
						foreach ($selected_themes as $key => $value)
						{
							$theme_id = $key;
							$theme_project = $this->ThemeProject->find("theme_id = ".$theme_id." AND project_id = ".$pid);

							if($total_projects==-1)
							{
								$theme = $this->Theme->find('Theme.id = '.$theme_id);
								$total_projects = $theme['Theme']['total_projects'];
							}
							else
							{
								$theme = $this->Theme->find('Theme.id = '.$theme_id.' AND total_projects = '.$total_projects);
							}
							$theme['Theme']['total_projects']--;
							$total_projects--;

							$this->Theme->id = $theme_id;
							$this->Theme->save($theme);

							if (!empty($theme_project['ThemeProject']))
							{
								$this->ThemeProject->id = $theme_project['ThemeProject']['id'];
								$this->ThemeProject->del();
							}
						}
					}
				}
			}
			$this->setFlash("Projects removed from themes", FLASH_NOTICE_KEY);
			$this->redirect('/users/' . $user_data['User']['urlname']);
		}
		exit;
	}


	/**
	 * Adds a comment to a themes discussion list
	 * @params int $theme_id => theme id to add comment
	 */
	function comment($theme_id) {
	    $this->exitOnInvalidArgCount(1);
        $this->Theme->id=$theme_id;
        $theme = $this->Theme->read();

        if (empty($theme))
            exit();

		$commenter_id = null;

        if ($this->activeSession())
        if (!empty($this->params['form'])) {
            $commenter_id = $this->Session->read('User.id');
            if ($commenter_id) {
                $comment = htmlspecialchars($this->params['form']['tcomment_textarea']);
				if(isInappropriate($comment)) {
					$vis = 0;
					$this->notify($commenter_id, 'We remind you to use appropriate language for all ages, please read the <a href="/terms">Community Guidelines</a>', false, false);
				} else {
					$vis = 1;
				}
                $new_tcomment = array('Tcomment'=>array('theme_id'=>$theme_id, 'user_id'=>$commenter_id, 'content'=>$comment, 'visibility'=>$vis));
				$this->Tcomment->id=null;
				$this->Tcomment->save($new_tcomment);
            }
        }
	$this->set('theme_id', $theme_id);
	$this->set('isThemeOwner', $commenter_id == $theme['Theme']['user_id']);
        $this->set('theme_comments',$this->Tcomment->findAll("theme_id = $theme_id and visibility = 1", null, "Tcomment.timestamp ASC"));

	$commenter_userrecord = $this->User->find("id = $commenter_id");
	$commenter_username = $commenter_userrecord['User']['username'];
	$user_id = $theme['Theme']['user_id'];
	$user_record = $this->User->find("id = $user_id");
	$notify_gcomment = $user_record['User']['notify_gcomment'];
	if($notify_gcomment && $vis==1 && $theme['Theme']['user_id']!=$commenter_id)
	{
		$guser_id = $theme['Theme']['user_id'];
		$user_record = $this->User->find("id = $guser_id");
		$username = $user_record['User']['username'];
		$theme_title = $theme['Theme']['name'];
		$message = 'Your theme <a href="/galleries/'.$theme_id.'">'.$theme_title.'</a> has received a new comment by <a href="/users/'.$commenter_username.'">'.$commenter_username.'</a>.';
		$this->notify($guser_id, $message, false);
	}

        $this->render('themecomments_ajax', 'ajax');
        return;
	}


	function delcomment($theme_id=null, $comment_id=null)
	{
		$this->exitOnInvalidArgCount(2);
        $this->autoRender=false;
		$user_id = $this->getLoggedInUserID();
        $this->Theme->id=$theme_id;
        $theme = $this->Theme->read();

		if (empty($theme))
			$this->cakeError('error404');

		if (!$this->isAdmin())
			if ($theme['User']['id'] !== $user_id)
				$this->cakeError('error404');

		$this->Tcomment->id = $comment_id;
		$this->Tcomment->saveField("visibility", null) ;

        $this->set('theme_id', $theme_id);
	$this->set('isThemeOwner', $user_id == $theme['User']['id']);
	$this->set('theme_comments',$this->Tcomment->findAll("theme_id = $theme_id and visibility = 1", null, "Tcomment.timestamp ASC"));
        $this->render('themecomments_ajax', 'ajax');
	}



	/**
     * updates theme picture
     */
	function updatepic($theme_id) {
		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->cakeError('error404');

		if (empty($this->params["form"]))
			$this->cakeError('error404');

		$this->Theme->id=$theme_id;
        $theme = $this->Theme->read();

		if (empty($theme))
			$this->cakeError('error404');

		if (!$this->isAdmin())
			if ($theme['User']['id'] !== $user_id)
				$this->cakeError('error404');
		$icon_array = $this->params["form"]["theme_icon"];
		$theme_icon_file = WWW_ROOT . getThemeIcon($theme_id, false, DS);
		mkdirR(dirname($theme_icon_file) . DS);
		$error = $this->FileUploader->handleFileUpload($icon_array, $theme_icon_file);
		if ($error)
		{
			$this->setFlash($error, FLASH_ERROR_KEY);
		}
		else
		{
			$this->setFlash("Theme image updated, reload page to view change", FLASH_NOTICE_KEY);
		}
		$this->redirect('/galleries/'.$theme_id);
	}


	/**
	 * Renders a theme page, a pagination listing of themes, or a paginated listing
	 * of theme projects
	 * @param int $theme_id => theme id
	 * @param string $browse_flag => if set, renders paginated lising of projects in theme_id
	 */
    function view($theme_id=null, $browse_flag=null) {
		$this->autoRender = false;

        if ($theme_id && $browse_flag)
        {
			$theme =  $this->Theme->read(null, $theme_id);
            if (empty($theme))
                $this->cakeError('error404');

            $this->modelClass = "ThemeProject";
			$criteria = Array("theme_id" => $theme_id);
            $options = Array("url"=>"/galleries/" . $theme_id . "/browse");
			list($order,$limit, $page) = $this->Pagination->init($criteria, $options);

			$this->ThemeProject->bindProject();
			$this->Project->bindUser();
			$theme_projects = $this->ThemeProject->findAll($criteria, NULL, $order, $limit, $page, 2);

			$this->set('theme_projects', $theme_projects);
            $this->set('theme', $theme);
            $this->render('projects');
		}
		else if ($theme_id)
		{
			$options = Array("url"=>"/galleries/".$theme_id);
			$this->modelClass = "ThemeProject";
			$this->Pagination->sortByClass = "Project";
			list($order,$limit,$page) = $this->Pagination->init("theme_id = $theme_id", $options, null, 15);			
			$this->Theme->bindHABTMProject(null, $order, $limit, $page);
			$this->Project->bindUser();
			$this->Theme->bindTcomment(array('visibility' => 1));
			$theme = $this->Theme->find("Theme.id = $theme_id", NULL, NULL, 2);

			if (empty($theme))
				$this->cakeError('error404');

			$this->set('theme', $theme['Theme']);
			$this->set('theme_id', $theme_id);

			$this->set('theme_owner', $theme['User']);
			$this->set('theme_projects', $theme['Project']);
			$this->set('theme_comments', $theme['Tcomment']);

			$sessionUID = $this->getLoggedInUserID();
			$isThemeOwner = $theme['User']['id'] == $sessionUID;
			$this->set('isThemeOwner', $isThemeOwner);
			$this->set('isFeatured', $this->FeaturedTheme->hasAny("theme_id = $theme_id"));
			$this->set('isThemeMember', false);
			$this->set('isClubbed', $this->ClubbedTheme->hasAny("theme_id = $theme_id"));

			if ($sessionUID) // isloggedIn
			{
				if ($isThemeOwner)
				{
					$this->set('isThemeMember', true);
				}
				else
				{
					$member_record = $this->ThemeMembership->find("theme_id = $theme_id AND user_id = $sessionUID");
					$isThemeMember = false;
					if (!empty($member_record))
						$isThemeMember = true;
					$this->set('isThemeMember', $isThemeMember);
				}
			}
			$this->render('themepage','scratchr_themepage');
        }
        else
        {
			$this->pageTitle = "Scratch | Galleries";
            $this->modelClass = "Theme";
            $options = Array("url"=>"/galleries");
            list($order,$limit,$page) = $this->Pagination->init("total_projects > 0", $options, 'total_projects');
            $data = $this->Theme->findAll("total_projects > 0", NULL, $order, $limit, $page); // display only galleries which have atleast 1 project
        	$this->set('data',$data);
            $this->render('explorer');
        }
    }

	/**
	 * Counts the number of projects for each gallery in the database
	 * and stores it in a separate field in the galleries table
	 */
    function countprojects() {
    	  $count = 0;
    	  $themes = $this->Theme->findAll();
	  foreach($themes as $theme) {
	  	$themeid = $theme['Theme']['id'];
	  	$totalProjects = $this->ThemeProject->findCount("theme_id = $themeid");
		
		$this->Theme->id = $themeid;
		if($this->Theme->saveField("total_projects",$totalProjects)) {
			$count++;
		}
	  }
	  $this->set('count',$count);
	  $this->render('countprojects');
    }
}
?>
