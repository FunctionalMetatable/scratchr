<?php
Class LatestController extends AppController {
    var $helpers = array('Pagination');
    var $components = array('Pagination');
    var $uses = array("IgnoredUser", "Project" ,'ProjectTag', 'Tag' ,"FeaturedProject", "Gallery", "Notification", "Pcomment");
	var $feed_links = array ();
	
	 /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$limit = 10;
		$this->Pagination->show = $limit;
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$obscured_uid = $this->encode($this->getLoggedInUserID());
        $this->feed_links = array (
            'shared' => "/feeds/getNewestProjects",
            'topviewed' => "/feeds/getLatestTopViewedProjects",
            'toploved' => "/feeds/getLatestTopLovedProjects",
            'remixed' => "/feeds/getLatestTopRemixedProjects",
            'givefeedback' => "/feeds/getGiveFeedback"
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
            $final_projects = $this->Project->findAll("Project.proj_visibility = 'visible' AND Project.status != 'notsafe'", NULL, 'Project.created DESC', NUM_LATEST_SHARED, 1, NULL, $this->getContentStatus());
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
            $final_projects = $this->Project->findAll($condition, NULL, 'Project.views DESC', NUM_LATEST_TOPVIWED, 1, NULL, $this->getContentStatus());
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
            $final_projects = $this->Project->findAll($condition, NULL, 'Project.loveitsuniqueip DESC', NUM_LATEST_TOPLOVED, 1, NULL, $this->getContentStatus());
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
			$final_projects = $this->Project->findAll($condition, NULL, 'Project.remixer DESC', NUM_LATEST_REMIXED, 1, NULL);
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
	
	
	
	function givefeedback() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = ___("Scratch | Active member's projects", true);
        $isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		
		$key = 'latest-activemember-';
        $ttl = LATEST_ACTIVEMEMBER_CACHE_TTL;
		$days = ACTIVEMEMBER_PROJECT_MAX_DAYS;
		$tcomment = NUM_LATEST_COMMENT;
		$num_result = NUM_LATEST_GETFEEDBACK;
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
													ORDER BY Project.created ASC LIMIT $num_result";
		
		
		$this->Project->mc_connect();
        $mc_key = $key.$days;
        $final_projects = $this->Project->mc_get($mc_key);
        if ($final_projects === false) {
           $this->Project->bindPcomment();
			$this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
			
            $final_projects = $this->Project->query($condition);
           
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
       
		$this->set('data', $final_projects);
        $this->Project->mc_close();
		
		$this->set('heading', ___("givefeedback", true));
		$this->set('option', 'givefeedback');
		$this->set('rss_link', $this->feed_links['topviewed']);
        $this->render('givefeedback');
    }
	
	  function tag($tag_name, $option = "views"){
   		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
            $this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$tag_id = $tag['Tag']['id'];
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND ProjectTag.tag_id = $tag_id GROUP BY project_id";

        if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
			$count_criteria = "Project.status = 'safe' AND " . $count_criteria; 
		}
		
		if ($option == "views") {
			$order = 'Project.views DESC';
		}
		
		if ($option == "loveits") {
			$order = 'Project.loveitsuniqueip DESC';
		}
		
		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$limit = NUM_LATEST_TAGED_PROJECT;
		$mc_key = 'project_tag_'.$tag_id.'_'.$content_status.'_'. $order.'_'. $limit;
		$ttl = LATEST_TAGED_PROJECT_CACHE_TTL; 
        $this->ProjectTag->mc_connect();
        $tag_projects = $this->ProjectTag->mc_get($mc_key);
        if($tag_projects === false) {
			$this->Project->unbindModel(array('hasMany' => array('GalleryProject')));
			$this->ProjectTag->unbindModel(array('belongsTo' => array('User')));
			$this->ProjectTag->recursive = 2;	
			$tag_projects = $this->ProjectTag->findAll($final_criteria, NULL , $order, NUM_LATEST_TAGED_PROJECT);
			$tag_projects = $this->set_projects($tag_projects);
			$this->ProjectTag->mc_set($mc_key, $tag_projects, false, $ttl);
		}
		$this->ProjectTag->mc_close();
		$this->set('option', $option);
        $this->set('tag_projects', $tag_projects);
		$this->set('tag_name', $tag_name);
        $this->set('tag', $tag);
        $this->render('projects');
   
   }//function
	
    
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
	
	
function _getProjectsCount($condition, $key, $ttl, $recursion = -1) {
        $this->Project->mc_connect();
        $model_class = $this->modelClass;
        $mc_key = $key.'count';
		$projects_count = $this->Project->mc_get($mc_key);
        if($projects_count === false) {
		$projects = $this->$model_class->query($condition, $recursion);
			$projects_count =  count($projects);
            $this->Project->mc_set($mc_key, $projects_count, false, $ttl);
        }
        $this->Project->mc_close();
        return $projects_count;
    }
}
?>