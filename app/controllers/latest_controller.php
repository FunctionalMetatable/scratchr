<?php
Class LatestController extends AppController {
    var $helpers = array('Pagination');
    var $components = array('Pagination');
    var $uses = array("IgnoredUser", "Project", "FeaturedProject", "Gallery", "Notification", "Pcomment");
	var $feed_links = array ();
	
	 /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$obscured_uid = $this->encode($this->getLoggedInUserID());
        $this->feed_links = array (
            'shared' => "/feeds/getNewestProjects",
            'topviewed' => "/feeds/getLatestTopViewedProjects",
            'toploved' => "/feeds/getLatestTopLovedProjects",
            'remixed' => "/feeds/getLatestTopRemixedProjects",
            'activemembers' => "/feeds/getActiveMembersProject/".$obscured_uid
        );
		$this->set('feed_links', $this->feed_links);
		$this->set('content_status', $this->getContentStatus());
        $this->set('isLoggedIn', $this->isLoggedIn());
    }
	
    function shared() {
		$this->layout = 'scratchr_explorer';
        $this->pageTitle = "Scratch | Newest projects";
                
        $key = 'latest-shared-';
        $ttl = LATEST_SHARED_CACHE_TTL;
       
        $this->Project->mc_connect();
        $mc_key = $key;
        $final_projects = $this->Project->mc_get($mc_key);
        if ($final_projects === false) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            $final_projects = $this->Project->findAll("Project.proj_visibility = 'visible' AND Project.status != 'notsafe'", NULL, 'Project.created DESC', 100, 1, NULL, $this->getContentStatus());
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $this->set('heading', ___("Recently shared projects", true));
        $this->set('option', 'shared');
        $this->set('rss_link', $this->feed_links['shared']);
        $this->render('explorer');
    }
	
    function topviewed() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = ___("Scratch | Top viewed projects", true);
        
		$key = 'latest-topviewed-';
        $ttl = LATEST_TOPVIEWED_CACHE_TTL;
       
        $this->Project->mc_connect();
        $mc_key = $key;
        $final_projects = $this->Project->mc_get($mc_key);
        if ($final_projects === false) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
			if ($this->getContentStatus() == 'safe') {
		    $days = TOP_VIEWED_DAY_INTERVAL_SAFE;
        	} else {
		    $days = TOP_VIEWED_DAY_INTERVAL;
        	}
			$condition = "`Project`.`user_id` > 0 AND `Project`.`created` > now( ) - INTERVAL $days  DAY AND  `Project`.`proj_visibility` = 'visible' AND `Project`.`status` <> 'notsafe'";
            $final_projects = $this->Project->findAll($condition, NULL, 'Project.views DESC', 100, 1, NULL, $this->getContentStatus());
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();
	
        $this->set('heading', ___("What the community has been viewing", true));
		$this->set('option', 'topviewed');
		$this->set('rss_link', $this->feed_links['topviewed']);
        $this->render('explorer');
    }

    function toploved() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = ___("Scratch | Top loved projects", true);
       
        $key = 'latest-toploved-';
        $ttl = LATEST_TOPLOVED_CACHE_TTL;
       
        $this->Project->mc_connect();
        $mc_key = $key;
        $final_projects = $this->Project->mc_get($mc_key);
        if ($final_projects === false) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
			$days = TOP_LOVED_DAY_INTERVAL;
			$condition = "`Project`.`user_id` > 0 AND `Project`.`created` > now( ) - INTERVAL $days  DAY AND Project.loveitsuniqueip > 0 AND `Project`.`proj_visibility` = 'visible' AND `Project`.`status` <> 'notsafe'";
            $final_projects = $this->Project->findAll($condition, NULL, 'Project.loveitsuniqueip DESC', 100, 1, NULL, $this->getContentStatus());
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $this->set('heading', ___("What the community has been loving", true));
        $this->set('option', 'toploved');
        $this->set('rss_link', $this->feed_links['toploved']);
        $this->render('explorer');
    }


	 function remixed() {
		$this->layout = 'scratchr_explorer'; 
		$this->pageTitle = ___("Scratch | Most Remixed projects", true);
      
        $key = 'latest-remixed-';
        $ttl = LATEST_REMIXED_CACHE_TTL;        
       
		$this->Project->mc_connect();
        $mc_key = $key;
        $final_projects = $this->Project->mc_get($mc_key);
        if ($final_projects === false) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            if ($this->getContentStatus() =='safe') {
		    $days = TOP_REMIXED_DAY_INTERVAL_SAFE;
			} else {
				$days = TOP_REMIXED_DAY_INTERVAL;
			}
			$condition = "`Project`.`user_id` > 0 AND `Project`.`created` > now( ) - INTERVAL $days  DAY AND remixer > 0 AND  `Project`.`proj_visibility` = 'visible' AND `Project`.`status` <> 'notsafe'";
			$final_projects = $this->Project->findAll($condition, NULL, 'Project.remixer DESC', 100, 1, NULL);
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $this->set('data', $final_projects);
        $this->set('heading', ___("What the community has been remixing", true));
		$this->set('option', 'remixed');
		$this->set('rss_link', $this->feed_links['remixed']);
        $this->render('explorer');
    }
	
	
	
	function activemembers() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = ___("Scratch | Active member's projects", true);
        $isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		$key = 'latest-activemember-';
        $ttl = LATEST_ACTIVEMEMBER_CACHE_TTL;
		$days = ACTIVEMEMBER_PROJECT_MAX_DAYS;
		
		$this->Pagination->show = 10;
		$this->modelClass = "Project";
        $options = array("sortBy"=>"created", "direction" => "DESC");
		list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id AND `Project`.`created` > now( ) - INTERVAL $days DAY AND Project.proj_visibility = 'visible' AND Project.status != 'notsafe'", Array(), $options);
        
        $this->Project->mc_connect();
        $mc_key = $key.$limit.'-'.$page;
        $final_projects = $this->Project->mc_get($mc_key);
        if ($final_projects === false) {
           $this->Project->bindPcomment();
			$this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
			
            $final_projects = $this->Project->findAll("Project.user_id = $user_id AND `Project`.`created` > now( ) - INTERVAL $days DAY AND Project.proj_visibility = 'visible' AND Project.status != 'notsafe'", NULL, $order, $limit, $page, NULL, $this->getContentStatus());
           
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
       
		$this->set('data', $final_projects);
        $this->Project->mc_close();
		$total_comment = $this->_getCommentsCount($key, $ttl);
		
        $this->set('comment_count', $total_comment);
		$this->set('heading', ___("active members", true));
		$this->set('option', 'activemembers');
		$this->set('rss_link', $this->feed_links['topviewed']);
        $this->render('activemembers');
    }
	
    
	function set_projects($projects) {
		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		$return_projects = Array();
		
		foreach ($projects as $project) {
			$temp_project = $project;
			$current_user_id = $temp_project['Project']['user_id'];
			$current_project_id = $temp_project['Project']['id'];
			$temp_project['Project']['ignored'] = false;
			if ($isLogged) {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $current_user_id");
				if ($ignore_count > 0) {
					$temp_project['Project']['ignored'] = true;
				} else {
					$temp_project['Project']['ignored'] = false;
				}
			}
			if(SHOW_RIBBON ==1){
			$temp_project['Project']['ribbon_name'] = $this->set_ribbon($current_project_id);
			
			}
			array_push($return_projects, $temp_project);
		}
		return $return_projects;
	}

   function set_ribbon($current_project_id) {
		$feature_projects = $this->FeaturedProject->find("FeaturedProject.project_id = $current_project_id");
		$image_name = '';
				if ($feature_projects) {
					$text =$this->convertDate($feature_projects['FeaturedProject']['timestamp']);
			 		$image_name =$this->ribbonImageName($feature_projects['FeaturedProject']['timestamp']);
			 		$this->Thumb->generateThumb($ribbon_image='ribbon.gif',$text,$dir="small_ribbon",$image_name,$dimension='40x30',125,125);
					}
					return $image_name;
	}
	
	
	 function _getCommentsCount($key, $ttl) {
        $this->Pcomment->mc_connect();
        $user_id = $this->getLoggedInUserID();
		$days = ACTIVEMEMBER_PROJECT_MAX_DAYS;
        $mc_key = $key.'comment-count';
        $comments_count = $this->Pcomment->mc_get($mc_key);
        if($comments_count === false) {
            $comments_count = $this->Pcomment->findCount("Project.user_id = $user_id AND Project.proj_visibility = 'visible' AND Project.status != 'notsafe' AND `Project`.`created` > now( ) - INTERVAL $days  DAY");
            $this->Pcomment->mc_set($mc_key, $comments_count, false, $ttl);
        }
        $this->Project->mc_close();
        return $comments_count;
    }
   
}
?>