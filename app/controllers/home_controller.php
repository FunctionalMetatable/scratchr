<?php
Class HomeController extends AppController {
    /**
     * Home Page Controller
     */
	var $pageTitle = "Scratch | Home | imagine, program, share";
    var $uses = array("Project","User","FeaturedProject", "ClubbedGallery","UserStat", "Gallery", "Theme", "FeaturedGallery", "Tag", "Notification","Curator","Favorite", "BlockedUserFrontpage", "ClubbedTheme", "ThemeProject");
    var $helpers = array("Tagcloud");
   	
	/**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
    function index() {
        $this->Project->mc_connect();
        $project_ids = array();
       	$user_ids =array();
        $this->set('client_ip', $this->RequestHandler->getClientIP());
        $home_projects = $this->Project->mc_get('home_projects_data');
        if(!$home_projects) {
			$curator_name = $this->___getCuratorName();
			
			$frontpage_blocked_user = $this->__getBlockedUserFrontpage();
			$frontpage_blocked_user_id = Set::extract('/BlockedUserFrontpage/user_id', $frontpage_blocked_user);
			$user_ids       =array_merge($user_ids, $frontpage_blocked_user_id);
			
			$clubedprojects = $this->__getDesignStudioProjects($user_ids);
			$clubed_project_id = Set::extract('/ThemeProject/project_id', $clubedprojects);
			$clubed_project_user_ids   = Set::extract('/Project/user_id', $clubedprojects);
			$project_ids    = array_merge($project_ids, $clubed_project_id);
			$user_ids       = array_merge($user_ids, $clubed_project_user_ids);
			
			$favorites = $this->__getCuratorFavorites($project_ids, $user_ids);
			$favorites_id = Set::extract('/Project/id', $favorites);
			$favorites_user_ids   = Set::extract('/Project/user_id', $favorites);
            $this->Project->register_frontpage($favorites_id, 'curator_favorites');
			$project_ids    = array_merge($project_ids, $favorites_id);
            $user_ids       = array_merge($user_ids, $favorites_user_ids);
            
            $featured       = $this->__getFeaturedProjects($project_ids, $user_ids);
            $featured_ids   = Set::extract('/FeaturedProject/project_id', $featured);
			$featured_user_ids   = Set::extract('/Project/user_id', $featured);
            $this->Project->register_frontpage($featured_ids, 'featured');
            $project_ids    = array_merge($project_ids, $featured_ids);
            $user_ids       = array_merge($user_ids, $featured_user_ids);
			
            $topremixed     = $this->__getTopRemixedProjects($project_ids, $user_ids);
            $topremixed_ids = Set::extract('/Project/id', $topremixed);
			$topremixed_user_ids = Set::extract('/Project/user_id', $topremixed);
            $this->Project->register_frontpage($topremixed_ids, 'top_remixed');
            $project_ids    = array_merge($project_ids, $topremixed_ids);
			$user_ids       = array_merge($user_ids, $topremixed_user_ids);
			
            $toploved       = $this->__getTopLovedProjects($project_ids, $user_ids);
            $toploved_ids   = Set::extract('/Project/id', $toploved);
			$toploved_user_ids   = Set::extract('/Project/user_id', $toploved);
            $this->Project->register_frontpage($toploved_ids, 'top_loved');
            $project_ids    = array_merge($project_ids, $toploved_ids);
			$user_ids       = array_merge($user_ids, $toploved_user_ids);
			
            $topviewed      = $this->__getTopViewedProjects($project_ids, $user_ids);
            $topviewed_ids  = Set::extract('/Project/id', $topviewed);
			$topviewed_user_ids  = Set::extract('/Project/id', $topviewed);
            $this->Project->register_frontpage($topviewed_ids, 'top_viewed');
            $project_ids    = array_merge($project_ids, $topviewed_ids);
			$user_ids       = array_merge($user_ids, $topviewed_user_ids);

           // $topdownloaded  = $this->__getTopDownloadedProjects($project_ids, $user_ids);
           // $topdownloaded_ids  = Set::extract('/Project/id', $topdownloaded);
           // $this->Project->register_frontpage($topdownloaded_ids, 'top_downloaded');
		 
			$home_projects = array('featured' => $featured,
                                   'topremixed' => $topremixed,
                                   'toploved' => $toploved,
                                   'topviewed' => $topviewed,
                                   //'topdownloaded' => $topdownloaded,
								   'favorites' => $favorites,
								   'curator_name' =>$curator_name,
								   'clubedprojects' => $clubedprojects
                                );
                                
            $this->Project->mc_set('home_projects_data', $home_projects,
                                    false, HOMEL_PAGE_TTL);
        }
        else {
            $featured       = $home_projects['featured'];
            $topremixed     = $home_projects['topremixed'];
            $toploved       = $home_projects['toploved'];
            $topviewed      = $home_projects['topviewed'];
            //$topdownloaded  = $home_projects['topdownloaded'];
			$favorites      = $home_projects['favorites'];
			$curator_name   = $home_projects['curator_name'];
			$clubedprojects   = $home_projects['clubedprojects'];
        }
		
        $this->set('featuredprojects', $featured);
        $this->set('topremixed', $topremixed);
        $this->set('toploved', $toploved);
        $this->set('topviewed', $topviewed);
        //$this->set('topdownloaded', $topdownloaded);
		$this->set('favorites', $favorites);
		$this->set('username',$curator_name);
		$this->set('clubedprojects',$clubedprojects);
		
		
		if($this->isLoggedIn()) {
            $myfriendsprojects = $this->Project->getMyFriendsLatest3Projects($this->getLoggedInUserID());
            $this->set('friendsprojects', $myfriendsprojects);
            
            $newprojects = $this->Project->mc_get("newprojects");
            if (!$newprojects) {
                $newprojects = $this->__getNewProjects();
                $this->Project->mc_set("newprojects", $newprojects, false, HOMEL_NEW_PROJECTS_TTL);
            }
            $this->set('newprojects', $newprojects);
        }
        
/*
        $toprandoms = $memcache->get("$prefix-toprandoms");
        if ( !$toprandoms) {
       	    $toprandoms = $this->__getTopRandomProjects();
            $memcache->set("$prefix-toprandoms", $toprandoms, false, 300) or die ("Failed to save data at the server");
            $toprandoms_ids   = Set::extract('/Project/id', $toprandoms);
            $this->Project->register_frontpage($toprandoms_ids, 'surprise');
        }
        $this->set('toprandoms', $toprandoms);
*/      
        $scratch_club = $this->Project->mc_get("scratch_club");
        if (!$scratch_club) {
       	    $scratch_club = $this->__getScratchClub();
            $this->Project->mc_set("scratch_club", $scratch_club, false, HOME_SCRATCH_CLUB_TTL);
        }
        $this->set('scratch_club', $scratch_club);
        
        $featuredthemes = $this->Project->mc_get("featuredthemes");
        if (!$featuredthemes) {
       	    $featuredthemes = $this->__getFeaturedGalleries();
            $featuredthemes_ids   = Set::extract('/Gallery/id', $featuredthemes);
            $this->Gallery->register_frontpage($featuredthemes_ids, 'featured');
            $this->Project->mc_set("featuredthemes", $featuredthemes, false, HOME_FEATURED_THEMES_TTL);
        }
        $this->set('featuredthemes', $featuredthemes);
        
        $recentvisitors = $this->Project->mc_get("recentvisitors");
        if (!$recentvisitors) {
       	    $recentvisitors = $this->__getRecentVisitors();
            $this->Project->mc_set("recentvisitors", $recentvisitors, false, HOME_RECENT_VISITORS_TTL);
        }
        $this->set('recentvisitors', $recentvisitors);
        
        $newmembers = $this->Project->mc_get("newmembers");
        if (!$newmembers) {
       	    $newmembers = $this->__getNewMembers();
            $this->Project->mc_set("newmembers", $newmembers, false, HOME_NEW_MEMBERS_TTL);
        }
        $this->set('newmembers', $newmembers);
        
        $totalprojects = $this->Project->mc_get("totalprojects");
        if (!$totalprojects) {
       	    $totalprojects = $this->__getTotalProjects();
            $this->Project->mc_set("totalprojects", $totalprojects, false, HOME_TOTAL_PROJECTS_TTL);
        }
        $this->set('totalprojects', $totalprojects);
        
        $totalscripts = $this->Project->mc_get("totalscripts");
        if (!$totalscripts) {
       	    $totalscripts = $this->__getTotalScripts();
            $this->Project->mc_set("totalscripts", $totalscripts, false, HOME_TOTAL_SCRIPTS_TTL);
        }
        $this->set('totalscripts', $totalscripts);
        
        $totalsprites = $this->Project->mc_get("totalsprites");
        if (!$totalsprites) {
       	    $totalsprites = $this->__getTotalSprites();
            $this->Project->mc_set("totalsprites", $totalsprites, false, HOME_TOTAL_SPRITES_TTL);
        }
        $this->set('totalsprites', $totalsprites);
        
        $totalcreators = $this->Project->mc_get("totalcreators");
        if (!$totalcreators) {
       	    $totalcreators = $this->__getTotalCreators();
            $this->Project->mc_set("totalcreators", $totalcreators, false, HOME_TOTAL_CREATOR_TTL);
        }
        $this->set('totalcreators', $totalcreators);
        
        $totalusers = $this->Project->mc_get("totalusers");
        if (!$totalusers) {
       	    $totalusers = $this->__getTotalUsers();
            $this->Project->mc_set("totalusers", $totalusers, false, HOME_TOTAL_USERS_TTL);
        }
        $this->set('totalusers', $totalusers);
        
        $tags = $this->Project->mc_get("tags");
        if (!$tags) {
       	    $tags = $this->__getTagCloud();
            $this->Project->mc_set("tags", $tags, false, HOME_TAGS_TTL);
        }
        $this->set('tags', $tags);
        
		/*$countries = $memcache->get("$prefix-countries");
        if ( $countries == "" ) {
       	    $countriestmp = $this->__getTopCountries();
            $memcache->set("$prefix-countries", $countriestmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('countries', $countriestmp);
        } else {
            $this->set('countries', $countries);
        }*/
		
    	$this->Project->mc_close();
		
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$newest_feed_link = "/feeds/getNewestProjects";
		$featured_feed_link = "/feeds/getFeaturedProjects";
		
		$this->set('newest_feed_link', $newest_feed_link);
		$this->set('featured_feed_link', $featured_feed_link);
		$this->set('ishomepage', true);
    }
	
	function __getScratchClub() {
        $club = $this->ClubbedGallery->find(NULL, NULL, "ClubbedGallery.id DESC");
        if(empty($club)) return false;
        $club = $this->finalize_gallery($club);
        return  $club['Gallery']; 
	}
	
    function __getNewProjects() {
        return $this->Project->getTopProjects('`created`', '`created` DESC', null,
            null, null, NUM_NEW_PROJECTS);
    }

    function __getBlockedUserFrontpage(){
	return $this->BlockedUserFrontpage->findAll(null,'BlockedUserFrontpage.user_id');
	}
	
	function __getFeaturedProjects($exclude_project_ids, $exclude_user_ids) {
        $condition = '`projects`.`id` = `featured_projects`.`project_id`';
        $projects = $this->Project->getTopProjects('`featured_projects`.`id` `featured`',
                    '`featured` DESC', null, $exclude_project_ids, $exclude_user_ids,
                    NUM_FEATURED_PROJECTS,
                    $condition, '`featured_projects`');
        return $projects;
    }

    function __getTopViewedProjects($exclude_project_ids, $exclude_user_ids) {
        if ($this->getContentStatus() == 'safe') {
		    $days = 20;
        } else {
		    $days = 4;
        }
        return $this->Project->getTopProjects('`views`', '`views` DESC', $days,
            $exclude_project_ids, $exclude_user_ids, NUM_TOP_VIEWED);
    }

    function __getTopRemixedProjects($exclude_project_ids, $exclude_user_ids) {
        if ($this->getContentStatus() =='safe') {
		    $days = 20;
        } else {
		    $days = 10;
        }

        return $this->Project->getTopProjects('`remixer`', '`remixer` DESC', $days,
            $exclude_project_ids, $exclude_user_ids, NUM_TOP_REMIXED, 'remixer > 0');
    }

    function __getTopLovedProjects($exclude_project_ids, $exclude_user_ids) {
        return $this->Project->getTopProjects('`loveitsuniqueip`', '`loveitsuniqueip` DESC', '10',
            $exclude_project_ids, $exclude_user_ids, NUM_TOP_RATED);
    }

    function __getTopDownloadedProjects($exclude_project_ids, $exclude_user_ids) {
        $exclude_clause = '';
		$exclude_user_id_clause = '';
        if(!empty($exclude_project_ids)) {
           $exclude_clause = ' AND projects.id NOT IN ( '.implode($exclude_project_ids, ' , ').' )';
        }
		if(!empty($exclude_user_ids)) {
           $exclude_user_id_clause = ' AND projects.user_id NOT IN ( '.implode($exclude_user_ids, ' , ').' )';
        }

        $this->Project->bindUser();
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  " AND projects.status = 'safe'";
        }
        else {
            $onlysafesql =  " AND projects.status <> 'notsafe'";
        }
        $topdpids =  $this->Project->query("SELECT project_id, COUNT(*) FROM `downloaders`,`projects` WHERE projects.created > now()  - interval 7 day AND projects.id = downloaders.project_id AND proj_visibility = 'visible' $exclude_clause $exclude_user_id_clause $onlysafesql GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT 3");
		$sqlor = "Project.id = " . (isset($topdpids[0]['downloaders']['project_id'])?$topdpids[0]['downloaders']['project_id']:-1) . " OR ";
		$sqlor .= "Project.id = " . (isset($topdpids[1]['downloaders']['project_id'])?$topdpids[1]['downloaders']['project_id']:-1) . " OR ";
		$sqlor .= "Project.id = " . (isset($topdpids[2]['downloaders']['project_id'])?$topdpids[2]['downloaders']['project_id']:-1);		

        $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
        return $this->Project->findAll($sqlor . ' GROUP BY Project.user_id', NULL, NULL, 3, 1, NULL, $this->getContentStatus());
    }

	/* Selects NUM_TOP_RATED random projects, consecutively */	
	// Avoid using MySQL's random due to bad performance
	function __getTopRandomProjects() {
        $this->Project->bindUser();
        $result = $this->Project->query("SELECT MAX(projects.id) AS maxid FROM projects");
        $random = rand(1, $result[0][0]['maxid']);
        $query = "Project.id >= $random AND Project.status <> 'notsafe' AND Project.proj_visibility = 'visible'";

        $count = $this->Project->findCount($query);
        if ($count < NUM_TOP_RATED) $query = "Project.id <= ".($random+$count);
            return $this->Project->findAll($query, NULL, "Project.id", NUM_TOP_RATED, 1, NULL, $this->getContentStatus());
    }

    function __getNewMembers() {
        return $this->User->findAll(NULL, NULL, "User.created DESC", NUM_NEW_MEMBERS);
    }

    function __getRecentVisitors() {
        return $this->UserStat->findAll(NULL, NULL, "UserStat.lastin DESC", NUM_RECENT_VISITORS);
    }

    function __getFeaturedGalleries() {
        $featuredgalleries = $this->FeaturedGallery->findAll(NULL, NULL, "FeaturedGallery.id DESC", NUM_FEATURED_THEMES);
        return $this->finalize_galleries($featuredgalleries);
    }

    function __getTagCloud() {
        return $this->Tag->getProjectTagCloud(TAG_CLOUD_HOME);
    }

    function __getTotalProjects() {
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  "Project.status = 'safe'";
	    }
        $this->Project->recursive = -1;
        $resultset =  $this->Project->findCount($onlysafesql);
        return number_format(0.0 + $resultset);
    }

    function __getTotalProjects7days() {
        return $this->Project->findCount("");
    }

    function __getTotalUsers() {
        $resultset =  $this->User->findCount("villager=0");
	return number_format(0.0 + $resultset);
    }
		
    function __getTotalUsers7days() {
        return $this->Users->findCount("1=1");
    }
    function __getTotalScripts() {
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  "WHERE projects.status = 'safe'";
        }
        $resultset =  $this->Project->query("select sum(totalScripts) as totalscripts from projects $onlysafesql");
	return number_format(0.0 + $resultset[0][0]['totalscripts']);
    }

    function __getTotalSprites() {
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  "WHERE projects.status = 'safe'";
        }
        $resultset =  $this->Project->query("select sum(numberOfSprites) as totalsprites from projects $onlysafesql");    
	return number_format(0.0 + $resultset[0][0]['totalsprites']);
    }

    function __getTotalCreators() {
        $resultset =  $this->Project->query("select count(distinct(user_id)) as totalcreators from projects where user_id");
	return number_format(0.0 + $resultset[0][0]['totalcreators']);
    }
	
	function __getTopCountries(){
	return $this->User->query("SELECT count(*)as cnt, country FROM `users` group by country order by cnt desc  LIMIT ".NUM_TOP_COUNTRIES);
	
	}
	
	function __getCuratorFavorites($exclude_project_ids, $exclude_user_ids) {
        $curator = $this->Curator->find(null, array(), 'Curator.id DESC');
        $curator_id  = $curator['Curator']['user_id'];

        if(empty($curator_id)) { return null; }

        $condition = '`favorites`.`user_id` = '. $curator_id
                    .' AND `projects`.`user_id` <> '. $curator_id
                    .' AND `projects`.`id` = `favorites`.`project_id`';
        $projects = $this->Project->getTopProjects('`favorites`.`timestamp` `recency`',
                    '`recency` DESC', null, $exclude_project_ids, $exclude_user_ids,
                    NUM_CURATOR_FAV_PROJECT,
                    $condition, '`favorites`');


        return $projects;
	}
    
	function ___getCuratorName(){
    	$curator =$this->Curator->find(null,array(),'Curator.id DESC');
        return $curator['User']['urlname'];
	}
	
    function __getDesignStudioProjects($exclude_user_ids) {
		$clubbed_gallery = $this->__getScratchClub();
        $gallery_id = $clubbed_gallery['id'];
        
        if(empty($gallery_id)) { return null; }

        $condition = '`gallery_id` = '. $gallery_id
                        .' AND `projects`.`id` = `gallery_projects`.`project_id`';
        
        $projects = $this->Project->getTopProjects('', 'RAND()', null, null,
                    $exclude_user_ids, NUM_DESIGN_STUDIO_PROJECT,
                    $condition, '`gallery_projects`');

        return $projects;
	}//function	
}
?>
