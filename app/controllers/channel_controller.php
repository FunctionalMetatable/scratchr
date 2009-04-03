<?php
Class ChannelController extends AppController {
    var $helpers = array('Pagination');
    var $components = array('Pagination');
    var $uses = array("IgnoredUser", "Project", "FeaturedProject", "Gallery", "Notification");
	
	
	 /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$limit = 10;
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$newest_feed_link = "/feeds/getNewestProjects";
		$featured_feed_link = "/feeds/getFeaturedProjects";
		$topviewed_feed_link = "/feeds/getTopViewedProjects";
		$toploved_feed_link = "/feeds/getTopLovedProjects";
		$remixed_feed_link = "/feeds/getTopRemixedProjects";
		
		$this->Pagination->show = $limit;
		$this->set('remixed_feed_link', $remixed_feed_link);
		$this->set('topviewed_feed_link', $topviewed_feed_link);
		$this->set('toploved_feed_link', $toploved_feed_link);
		$this->set('newest_feed_link', $newest_feed_link);
		$this->set('featured_feed_link', $featured_feed_link);
		$this->set('content_status', $this->getContentStatus());
    }
	
    function recent() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = "Scratch | Newest projects";
        $this->modelClass = "Project";
        $options = array("sortBy"=>"created", "direction" => "DESC");
        
        $key = 'channel-recent-';
        $ttl = CHANNEL_RECENT_CACHE_TTL;
        $projects_count = $this->_getProjectsCount('proj_visibility = "visible"',
                                                $key, $ttl);
        list($order, $limit, $page) = $this->Pagination->init(null, array(),
                                            $options, $projects_count);

        $this->Project->mc_connect();
        $mc_key = $key.$limit.'-'.$page;
        $final_projects = $this->Project->mc_get($mc_key);
        if ( !$final_projects) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            $final_projects = $this->Project->findAll("Project.proj_visibility = 'visible'", NULL, $order, $limit, $page, NULL, $this->getContentStatus());
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $newest_feed_link = "/feeds/getNewestProjects";
		
        $this->set('heading', "new projects");
        $this->set('option', 'recent');
        $this->set('rss_link', $newest_feed_link);
        $this->render('explorer');
    }
	
    function featured() {
		$this->layout = 'scratchr_explorer';
		$this->pageTitle = ___("Scratch | Featured projects", true);
        $this->modelClass = "FeaturedProject";
		$options = array("sortByClass" => "FeaturedProject", "sortBy"=>"timestamp", "direction" => "DESC");

        $key = 'channel-featured-';
        $ttl = CHANNEL_FEATURED_CACHE_TTL;
        $projects_count = $this->_getProjectsCount('proj_visibility = "visible"',
                                                $key, $ttl, 0);
        list($order, $limit, $page) = $this->Pagination->init(null, array(),
                                            $options, $projects_count);
                                        
        $this->Project->mc_connect();
        $mc_key = $key.$limit.'-'.$page;
        $final_projects = $this->Project->mc_get($mc_key);
        if ( !$final_projects) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            $final_projects = $this->FeaturedProject->findAll("Project.proj_visibility = 'visible'", NULL, $order, $limit, $page, 2, $this->getContentStatus());
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $featured_feed_link = "/feeds/getFeaturedProjects";
		
        $this->set('heading', ___("featured projects", true));
        $this->set('option', 'featured');
		$this->set('rss_link', $featured_feed_link);
		$this->render('explorer');
    }

    function topviewed() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = ___("Scratch | Top viewed projects", true);
        $this->modelClass = "Project";
        $options = array("sortBy"=>"views", "direction" => "DESC");

        $key = 'channel-topviewed-';
        $ttl = CHANNEL_TOPVIEWED_CACHE_TTL;
        $projects_count = $this->_getProjectsCount('proj_visibility = "visible"',
                                                $key, $ttl);
        list($order, $limit, $page) = $this->Pagination->init(null, array(),
                                            $options, $projects_count);
                                            
        $this->Project->mc_connect();
        $mc_key = $key.$limit.'-'.$page;
        $final_projects = $this->Project->mc_get($mc_key);
        if ( !$final_projects) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            $final_projects = $this->Project->findAll("Project.proj_visibility = 'visible'", NULL, $order, $limit, $page, NULL, $this->getContentStatus());
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();
	
        $topviewed_feed_link = "/feeds/getTopViewedProjects";
	    $this->set('heading', ___("top viewed", true));
		$this->set('option', 'topviewed');
		$this->set('rss_link', $topviewed_feed_link);
        $this->render('explorer');
    }

    function toploved() {
        $this->layout = 'scratchr_explorer';
        $this->pageTitle = ___("Scratch | Top loved projects", true);
        $this->modelClass = "Project";
        $options = array("sortBy"=>"loveit", "direction" => "DESC");


        $key = 'channel-toploved-';
        $ttl = CHANNEL_TOPLOVED_CACHE_TTL;
        $projects_count = $this->_getProjectsCount('loveit > 0 AND proj_visibility = "visible"',
                                                $key, $ttl);
        list($order, $limit, $page) = $this->Pagination->init(null, array(),
                                            $options, $projects_count);

        $this->Project->mc_connect();
        $mc_key = $key.$limit.'-'.$page;
        $final_projects = $this->Project->mc_get($mc_key);
        if ( !$final_projects) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            $final_projects = $this->Project->findAll("Project.proj_visibility = 'visible'", NULL, $order, $limit, $page, NULL, $this->getContentStatus());
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $toploved_feed_link = "/feeds/getTopLovedProjects";
		$this->set('heading', ___("top loved", true));
        $this->set('option', 'toploved');
        $this->set('rss_link', $toploved_feed_link);
        $this->render('explorer');
    }


    function oldest() {
		$this->layout = 'scratchr_explorer'; 
		$this->pageTitle = "Scratch | Oldest projects";
        $this->modelClass = "Project";
        list($order,$limit,$page) = $this->Pagination->init("Project.proj_visibility = 'visible'");
        $this->Project->bindUser();
        $this->set('data', $this->Project->findAll("Project.proj_visibility = 'visible'",  NULL, "Project.id ASC", $limit, $page));
        $this->set('heading', "new projects");
        $this->render('explorer');
    }


    function untitled() {
		$this->layout = 'scratchr_explorer'; 
        $this->pageTitle = "Scratch | Oldest projects";
        $this->modelClass = "Project";
        list($order,$limit,$page) = $this->Pagination->init();
        $this->Project->bindUser();
        $this->set('data', $this->Project->findAll("name = 'Untitled'",  NULL, "Project.id ASC", $limit, $page));
        $this->set('heading', "new projects");
        $this->render('explorer');
    }

	 function remixed() {
		$this->layout = 'scratchr_explorer'; 
		$this->pageTitle = ___("Scratch | Most Remixed projects", true);
        $this->modelClass = "Project";
        $options = array("sortBy"=>"remixes", "direction" => "DESC");

        $key = 'channel-remixed-';
        $ttl = CHANNEL_REMIXED_CACHE_TTL;        
        $projects_count = $this->_getProjectsCount('remixes > 0 AND proj_visibility = "visible"',
                                                $key, $ttl);
        list($order, $limit, $page) = $this->Pagination->init(null, array(),
                                            $options, $projects_count);

		$this->Project->mc_connect();
        $mc_key = $key.$limit.'-'.$page;
        $final_projects = $this->Project->mc_get($mc_key);
        if ( !$final_projects) {
            $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
            $final_projects = $this->Project->findAll("remixes > 0 AND Project.proj_visibility = 'visible'", NULL, $order, $limit, $page, NULL);
            $final_projects = $this->set_projects($final_projects);
            $this->Project->mc_set($mc_key, $final_projects, false, $ttl);
        }
        $this->set('data', $final_projects);
        $this->Project->mc_close();

        $remixed_feed_link = "/feeds/getTopRemixedProjects";
		
        $this->set('data', $final_projects);
        $this->set('heading', ___("top remixed", true));
		$this->set('option', 'remixed');
		$this->set('rss_link', $remixed_feed_link);
        $this->render('explorer');
    }
	
	
 /* Methods to show surprise project(10 random projects)
 *
 * @author	Ashok gond
 */
	function surprise() {
		$this->layout = 'scratchr_explorer'; 
		$this->pageTitle = ___("Scratch | Surprise projects", true);
        $this->modelClass = "Project";
       
		$result = $this->Project->query("SELECT MAX(projects.id) AS maxid FROM projects");
        $random = rand(1, $result[0][0]['maxid']);
        $query = "Project.id >= $random AND Project.status <> 'notsafe' AND Project.proj_visibility = 'visible'";
        $count = $this->Project->findCount($query);
        if ($count < 10) $query = "Project.id <= ".($random+$count);

        $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
        );
        $final_projects = $this->Project->findAll($query, NULL, "Project.id", 10, 1, NULL);
		
        //$final_projects = $this->Project->findAll("remixes > 0", NULL, $order, $limit, $page, NULL);
		$final_projects = $this->set_projects($final_projects);
		
		$remixed_feed_link = "/feeds/getTopRemixedProjects";
		
        $this->set('data', $final_projects);
        $this->set('heading', ___("surprised", true));
		$this->set('option', 'surprised');
		$this->set('rss_link', $remixed_feed_link);
        $this->render('surprise');
    }
	
	
	function set_projects($projects) {
		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		$return_projects = Array();
		
		foreach ($projects as $project) {
			$temp_project = $project;
			$current_user_id = $temp_project['Project']['user_id'];
			$temp_project['Project']['ignored'] = false;
			if ($isLogged) {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $current_user_id");
				if ($ignore_count > 0) {
					$temp_project['Project']['ignored'] = true;
				} else {
					$temp_project['Project']['ignored'] = false;
				}
			}
			array_push($return_projects, $temp_project);
		}
		return $return_projects;
	}

    function _getProjectsCount($condition, $key, $ttl, $recursion = -1) {
        $this->Project->mc_connect();
        $model_class = $this->modelClass;
        $mc_key = $key.'count';
        $projects_count = $this->Project->mc_get($mc_key);
        if(!$projects_count) {
            $projects_count = $this->$model_class->findCount($condition, $recursion, 'all', true);
            $this->Project->mc_set($mc_key, $projects_count, false, $ttl);
        }
        $this->Project->mc_close();
        return $projects_count;
    }
}
?>
