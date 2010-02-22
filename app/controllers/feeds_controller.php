<?php
class FeedsController extends AppController {
    var $name = 'Feeds';
    var $components = array('Session', 'Cookie');
	var $uses = array('Flagger', 'User', 'FeaturedProject', 'Project', 'Gallery', 'GalleryProject','Notification');
	var $helpers = array('Template');
	/**
	* Test
	**/
	function test() {
		$this->layout = 'xml'; 
        $this->set('projects', $this->Project->findAll(null, null, null, 1));
	}
	
	function getNotificationFeeds($encoded_user_id){
		$this->autoRender = false;
		$this->layout = 'xml';
		$user_id = $this->decode($encoded_user_id);
		$user_record = $this->User->find("id = $user_id");
		if(empty($user_record)) {
			$this->cakeError('error404');
		}
		
		$username = $user_record['User']['username']; 
	
		$notifications = $this->Notification->getNotifications($user_id, 1, 10);
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getNotificationFeeds/".$encoded_user_id;
		
		$this->set('username', $username);
		$this->set('rss_link', $rss_link);
		$this->set('notifications',$notifications);
		$this->render('get_notifications_feeds');
	}
	
	function getNewestProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_newest_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Project->findAll("", null, "Project.created DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();

		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getNewestProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('newest_project_feed');

	}
	
	function getFeaturedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_featured_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->FeaturedProject->findAll("", null, "FeaturedProject.timestamp DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();
		
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getFeaturedProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('featured_project_feed');
	}
	
	function getTopViewedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_top_viewed_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Project->findAll("Project.proj_visibility = 'visible'", null, "Project.views DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();

		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getFeaturedProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('topviewed_project_feed');
	}
	
	function getTopLovedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		
		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_top_loved_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Project->findAll("Project.loveit > 0", null, "Project.loveit DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();

		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getTopLovedProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('toploved_project_feed');
	}
	
	function getTopFlaggedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_top_flagged_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Flagger->findAll("", null, "Flagger.timestamp DESC", 100);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();
		
		$final_projects = Array();
		$flagged_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getTopFlaggedProjects";
		
		$counter = 0;
		foreach ($projects as $flagger) {
			$project_id = $flagger['Flagger']['project_id'];
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			
			$current_project = $this->Project->find("Project.id = $project_id");
			$user_id = $current_project['Project']['user_id'];
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];

			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			
			if (in_array($project_id, $flagged_projects)) {
			} else {
				array_push($final_projects, $current_project);
				array_push($flagged_projects, $project_id);
			}
			$counter++;
			if ($counter >= 16) {
				break;
			}
		}

		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('topflagged_project_feed');
	}
	
	function getTopRemixedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_top_remixed_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Project->findAll("Project.remixes > 0", null, "Project.remixes DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();

		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getTopRemixedProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('topremixed_project_feed');
	}
	
	/**
	* RSS Feed for Recent Gallery Projects
	**/
	function getRecentUserProjects($user_id) {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		$this->User->id = $user_id;
		$user = $this->User->read();
		$user_name = $user['User']['username'];

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_recent_user_projects_' . $user_id;
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Project->findAll("user_id = $user_id AND proj_visibility = 'visible'", null, "Project.created DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();
		
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getRecentUserProjects/$user_id";

		$this->set('user_name', $user_name);
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects, $user_name));
		$this->render('User_project_feed');
	}
	
	/**
	* RSS Feed for Recent Gallery Projects
	**/
	function getRecentGalleryProjects($gallery_id) {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		
		$gallery_projects = $this->GalleryProject->findAll("gallery_id = $gallery_id AND Project.proj_visibility = 'visible'", null, "GalleryProject.timestamp DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getRecentGalleryProjects/$gallery_id";
		
		$this->set('gallery_name', $gallery_name);
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($gallery_projects));
		$this->render('gallery_project_feed');
	}
    
	function getFriendsLatestProjects($encoded_user_id){
		$this->autoRender = false;
		$this->layout = 'xml';
		$user_id = $this->decode($encoded_user_id);
		$user_record = $this->User->find("id = $user_id");
		if(empty($user_record)) {
			$this->cakeError('error404');
		}

		$username = $user_record['User']['username'];
		$projects = $this->Project->getMyFriendsLatestProjects($user_id, 0, 10);

		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getFriendsLatestProjects/".$encoded_user_id;

		$this->set('username', $username);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->set('rss_link', $rss_link);
		$this->render('get_friends_latest_feeds');
	}
	
	 function getLatestTopViewedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		$this->Project->unbindModel(
		    array('hasMany' => array('GalleryProject'))
		    );
		if ($this->getContentStatus() == 'safe') {
		    $days = TOP_VIEWED_DAY_INTERVAL_SAFE;
        	} else {
		    $days = TOP_VIEWED_DAY_INTERVAL;
        	}
		$condition = "`Project`.`user_id` > 0 AND `Project`.`created` > now( ) - INTERVAL $days  DAY AND  `Project`.`proj_visibility` = 'visible' AND `Project`.`status` <> 'notsafe'";

		# Check memcache first
		$this->Project->mc_connect();
		$mc_key = 'feeds_latest_top_viewed_projects';
		
		$projects = $this->Project->mc_get($mc_key);

		if($projects === false) {
		    $projects = $this->Project->findAll($condition, null, "Project.views DESC", 15);
		    $this->Project->mc_set($mc_key, $projects, false, FEEDS_TTL);
		}

		$this->Project->mc_close();

		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getLatestTopViewedProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('latest_topviewed_project_feed');
	}
	
	function getLatestTopLovedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		 $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
			$days = TOP_LOVED_DAY_INTERVAL;
			$condition = "`Project`.`user_id` > 0 AND `Project`.`created` > now( ) - INTERVAL $days  DAY AND Project.loveitsuniqueip > 0 AND `Project`.`proj_visibility` = 'visible' AND `Project`.`status` <> 'notsafe'";
		$projects = $this->Project->findAll($condition, null, "Project.loveitsuniqueip DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getLatestTopLovedProjects";
        $this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('latest_toploved_project_feed');
	}
	 
	 function getLatestTopRemixedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		$this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            if ($this->getContentStatus() =='safe') {
		    $days = TOP_REMIXED_DAY_INTERVAL_SAFE;
			} else {
				$days = TOP_REMIXED_DAY_INTERVAL;
			}
			$condition = "`Project`.`user_id` > 0 AND `Project`.`created` > now( ) - INTERVAL $days  DAY AND remixer > 0 AND  `Project`.`proj_visibility` = 'visible' AND `Project`.`status` <> 'notsafe'";
		$projects = $this->Project->findAll($condition, null, "Project.remixes DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getTopRemixedProjects";
		$this->set('rss_link', $rss_link);
		$this->set('projects', $this->__feedize_projects($projects));
		$this->render('latest_topremixed_project_feed');
	}
	
	 function getGiveFeedback(){
		$this->autoRender = false;
		$this->layout = 'xml';
		
		$days = ACTIVEMEMBER_PROJECT_MAX_DAYS;
		$tcomment = NUM_LATEST_COMMENT;
		
		$this->Project->bindPcomment();
			$this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
        $condition = 								"SELECT *
													FROM projects Project
													LEFT JOIN `users` AS `User` ON ( `Project`.`user_id` = `User`.`id` )
													WHERE DATEDIFF( CURRENT_DATE( ) , Project.created )
													BETWEEN 0
													AND $days
													AND  `Project`.`proj_visibility` = 'visible' 
													AND `Project`.`status` <> 'notsafe'
													AND (
													SELECT COUNT( * )
													FROM pcomments pc, projects b
													WHERE pc.project_id = Project.id
													AND b.user_id = Project.user_id
													) < $tcomment
													GROUP BY Project.user_id
													HAVING MAX(numberOfSprites*totalScripts)
													ORDER BY Project.created DESC LIMIT 0,10";
		$projects = $this->Project->query($condition);
		
        $url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getGiveFeedback";
		
		
        $this->set('projects', $this->__feedize_projects($projects));
		$this->set('rss_link', $rss_link);
        $this->render('get_give_feedback_feeds');
	}
	
	/*
     * makes $projects array feed ready
     */
    function __feedize_projects($projects, $user_name_param = null) {
        $url = strtolower(env('SERVER_NAME'));
        $user_name = $user_name_param;
        foreach ($projects as $key => $project) {
			$project_id = $project['Project']['id'];
            if(!$user_name_param) {
                $user_id = $project['Project']['user_id'];
                $user = $this->User->find("User.id = $user_id");
                $user_name = $user['User']['username'];
            }
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$project['Project']['link'] = "http://" . $url . $additional_url;
			$project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$project['Project']['description'] = 
                        str_replace("�", "-", $project['Project']['description']);
            $projects[$key] = $project;
		}

        return $projects;
    }
}
?>
