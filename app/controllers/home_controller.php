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
	
	/*
	function to set users country location to cookie
	*/
	function country($newCountry = null){
		if($this->isCustomizableCountry($newCountry)) {
			$this->Cookie->delete('country');
			$this->Cookie->write('country', $newCountry, null, '+350 day');
		}
		else if(empty($newCountry)) {
			$this->Cookie->delete('country');
		}
		
		$this->redirect('/');
	}

	/*
	overrides user's ip country
	*/
	function set_country($newCountry = null) {
		$this->Cookie->delete('ip_country');
		if(!empty($newCountry)) {
			$this->Cookie->delete('ip_country');
			$this->Cookie->write('ip_country', $newCountry, null, '+350 day');
		} 

		$this->redirect('/');
	}
	
    function index() {  
		//set users country
		$cookieCountry = $this->Cookie->read('country');
		//set cookie country, if cookie country is not set
		if(empty($cookieCountry)) {
			$ipCountryName = $this->getUserCountryFromIP();
			if($this->isCustomizableCountry($cookieCountry)) {
				$this->Cookie->write('country', $ipCountryName, null, '+350 day');
				$cookieCountry = $ipCountryName;
			}
		}
		
        $this->Project->mc_connect();
        $project_ids = array();
       	$user_ids =array();
        $this->set('client_ip', $this->RequestHandler->getClientIP());
		
		$key = 'home_projects_data';
		if($cookieCountry && strcmp($cookieCountry, DEFAULT_COUNTRY) != 0){
			$key = 'home_projects_data_'.$cookieCountry;
		}
		
        $home_projects = $this->Project->mc_get($key);
        if($home_projects === false) {
			$curator_name = $this->___getCuratorName();
			
			$frontpage_blocked_user = $this->__getBlockedUserFrontpage();
			$frontpage_blocked_user_id = Set::extract('/BlockedUserFrontpage/user_id', $frontpage_blocked_user);
			$user_ids       = array_merge($user_ids, $frontpage_blocked_user_id);
			
			/*The priority should be:
			featured > curator > top loved > top remixed > top viewed > design studio.*/
			//1.Featured
			$featured       = $this->__getFeaturedProjects($user_ids);
            $featured_ids   = Set::extract('/Project/id', $featured);
			$featured_user_ids   = Set::extract('/User/id', $featured);
            $this->Project->register_frontpage($featured_ids, 'featured');
            $project_ids    = array_merge($project_ids, $featured_ids);
            $user_ids       = array_merge($user_ids, $featured_user_ids);
			//2.Curator
			$favorites = $this->__getCuratorFavorites($project_ids, $user_ids);
			$favorites_id = Set::extract('/Project/id', $favorites);
			$favorites_user_ids   = Set::extract('/User/id', $favorites);
            $this->Project->register_frontpage($favorites_id, 'curator_favorites');
			$project_ids    = array_merge($project_ids, $favorites_id);
            $user_ids       = array_merge($user_ids, $favorites_user_ids);
			//3.Top Loved
			/*Fetch conutry based project*/
			if($key !== 'home_projects_data'){
				$toploved = $this->__getTopLovedProjects($project_ids, $user_ids, $cookieCountry);
			}
			if(empty($toploved)) {
				$toploved = $this->__getTopLovedProjects($project_ids, $user_ids);
			}
            $toploved_ids   = Set::extract('/Project/id', $toploved);
			$toploved_user_ids   = Set::extract('/User/id', $toploved);
            $this->Project->register_frontpage($toploved_ids, 'top_loved');
            $project_ids    = array_merge($project_ids, $toploved_ids);
			$user_ids       = array_merge($user_ids, $toploved_user_ids);
			//4.Top Remixed
			/*Fetch conutry based project*/
			if($key !== 'home_projects_data'){
				$topremixed = $this->__getTopRemixedProjects($project_ids, $user_ids, $cookieCountry);
			}
			if(empty($topremixed)) {
				$topremixed = $this->__getTopRemixedProjects($project_ids, $user_ids);
			}
            $topremixed_ids = Set::extract('/Project/id', $topremixed);
			$topremixed_user_ids = Set::extract('/User/id', $topremixed);
            $this->Project->register_frontpage($topremixed_ids, 'top_remixed');
            $project_ids    = array_merge($project_ids, $topremixed_ids);
			$user_ids       = array_merge($user_ids, $topremixed_user_ids);
			//5.Top Viwed
			/*Fetch conutry based project*/
			if($key !== 'home_projects_data'){
				$topviewed = $this->__getTopViewedProjects($project_ids, $user_ids, $cookieCountry);
			}
			if(empty($topviewed)) {
				$topviewed = $this->__getTopViewedProjects($project_ids, $user_ids);
			}
            $topviewed_ids  = Set::extract('/Project/id', $topviewed);
			$topviewed_user_ids  = Set::extract('/User/id', $topviewed);
            $this->Project->register_frontpage($topviewed_ids, 'top_viewed');
            $project_ids    = array_merge($project_ids, $topviewed_ids);
			$user_ids       = array_merge($user_ids, $topviewed_user_ids);
			//6.Design Studio			
			$clubedprojects = $this->__getDesignStudioProjects($project_ids, $user_ids);
            $clubedproject_ids = Set::extract('/Project/id', $clubedprojects);
			$clubed_project_user_ids   = Set::extract('/User/id', $clubedprojects);
            $this->Project->register_frontpage($clubedproject_ids, 'design_studio');
            $project_ids    = array_merge($project_ids, $clubedproject_ids);
			$user_ids       = array_merge($user_ids, $clubed_project_user_ids);
			
           // $topdownloaded  = $this->__getTopDownloadedProjects($project_ids, $user_ids);
           // $topdownloaded_ids  = Set::extract('/Project/id', $topdownloaded);
           // $this->Project->register_frontpage($topdownloaded_ids, 'top_downloaded');
		 
			$home_projects = array('featured' => $featured,
									'favorites' => $favorites,
									'curator_name' =>$curator_name,
									'toploved' => $toploved,
                                    'topremixed' => $topremixed,
                                    'topviewed' => $topviewed,
                                   //'topdownloaded' => $topdownloaded,
								    'clubedprojects' => $clubedprojects
                                );
                                
            $this->Project->mc_set($key, $home_projects,
                                    false, HOMEL_PAGE_TTL);
        }
        else {
            $featured       = $home_projects['featured'];
			$favorites      = $home_projects['favorites'];
			$curator_name   = $home_projects['curator_name'];
			$toploved       = $home_projects['toploved'];
            $topremixed     = $home_projects['topremixed'];
            $topviewed      = $home_projects['topviewed'];
            //$topdownloaded  = $home_projects['topdownloaded'];
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
		
		
		if ($this->isLoggedIn()) {
            $myfriendsprojects = $this->Project->getMyFriendsLatest3Projects($this->getLoggedInUserID());
            if(SHOW_RIBBON ==1){
				$myfriendsprojects = $this->set_ribbon($myfriendsprojects);
			}
			$this->set('friendsprojects', $myfriendsprojects);
            
            $newprojects = $this->Project->mc_get("newprojects");
            if ($newprojects === false) {
				//limit the search to only recent projects to improve performance
				// Otherwise we are forced to do a full table scan 1M+ rows which is expensive
				$lowerlimitforid = $this->Project->mc_get("totalprojects");
				if($lowerlimitforid === false) {
					$lowerlimitforid = 940000; // if there is no value in memcached this is a safe be as of 2010-03-24
				}
				$newprojects = $this->__getNewProjects(intval(str_replace(",","", $lowerlimitforid)) - 100);
                $this->Project->mc_set("newprojects", $newprojects, false, HOMEL_NEW_PROJECTS_TTL);
			}
			if(SHOW_RIBBON ==1){
				$newprojects = $this->set_ribbon($newprojects);
			}
            
            $this->set('newprojects', $newprojects);
            //$this->record_user_event('view_frontpage');
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
        if ($scratch_club === false) {
       	    $scratch_club = $this->__getScratchClub();
            $this->Project->mc_set("scratch_club", $scratch_club, false, HOME_SCRATCH_CLUB_TTL);
        }
        $this->set('scratch_club', $scratch_club);
        
        $featuredthemes = $this->Project->mc_get("featuredthemes");
        if ($featuredthemes === false) {
       	    $featuredthemes = $this->__getFeaturedGalleries();
            $featuredthemes_ids   = Set::extract('/Gallery/id', $featuredthemes);
            $this->Gallery->register_frontpage($featuredthemes_ids, 'featured');
            $this->Project->mc_set("featuredthemes", $featuredthemes, false, HOME_FEATURED_THEMES_TTL);
        }
        $this->set('featuredthemes', $featuredthemes);
        
        $recentvisitors = $this->Project->mc_get("recentvisitors");
        if ($recentvisitors === false) {
       	    $recentvisitors = $this->__getRecentVisitors();
            $this->Project->mc_set("recentvisitors", $recentvisitors, false, HOME_RECENT_VISITORS_TTL);
        }
        $this->set('recentvisitors', $recentvisitors);
        
        $newmembers = $this->Project->mc_get("newmembers");
        if ($newmembers === false) {
       	    $newmembers = $this->__getNewMembers();
            $this->Project->mc_set("newmembers", $newmembers, false, HOME_NEW_MEMBERS_TTL);
        }
        $this->set('newmembers', $newmembers);
        
        
		$totalVisibleProjects = $this->Project->mc_get("totalVisibleProjects");
        if ($totalVisibleProjects === false) {
       	    $totalVisibleProjects = $this->__getTotalVisibleProjects();
            $this->Project->mc_set("totalVisibleProjects", $totalVisibleProjects, false, HOME_TOTAL_VISIBLE_PROJECTS_TTL);
        }
        $this->set('totalVisibleProjects', $totalVisibleProjects);
		
		$totalprojects = $this->Project->mc_get("totalprojects");
        if ($totalprojects === false) {
       	    $totalprojects = $this->__getTotalProjects();
            $this->Project->mc_set("totalprojects", $totalprojects, false, HOME_TOTAL_PROJECTS_TTL);
        }
        $this->set('totalprojects', $totalprojects);
        
        $totalscripts = $this->Project->mc_get("totalscripts");
        if ($totalscripts === false) {
       	    $totalscripts = $this->__getTotalScripts();
            $this->Project->mc_set("totalscripts", $totalscripts, false, HOME_TOTAL_SCRIPTS_TTL);
        }
        $this->set('totalscripts', $totalscripts);
        
        $totalsprites = $this->Project->mc_get("totalsprites");
        if ($totalsprites === false) {
       	    $totalsprites = $this->__getTotalSprites();
            $this->Project->mc_set("totalsprites", $totalsprites, false, HOME_TOTAL_SPRITES_TTL);
        }
        $this->set('totalsprites', $totalsprites);
        
        $totalcreators = $this->Project->mc_get("totalcreators");
        if ($totalcreators === false) {
       	    $totalcreators = $this->__getTotalCreators();
            $this->Project->mc_set("totalcreators", $totalcreators, false, HOME_TOTAL_CREATOR_TTL);
        }
        $this->set('totalcreators', $totalcreators);
        
        $totalusers = $this->Project->mc_get("totalusers");
        if ($totalusers === false) {
       	    $totalusers = $this->__getTotalUsers();
            $this->Project->mc_set("totalusers", $totalusers, false, HOME_TOTAL_USERS_TTL);
        }
        $this->set('totalusers', $totalusers);
        
        $tags = $this->Project->mc_get("tags");
        if ($tags === false) {
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
	
	function __getNewProjects($lowerlimitforid) {
		return $this->Project->getTopProjects('`created`', '`created` DESC', null, null, null, NUM_NEW_PROJECTS,  "projects.id > $lowerlimitforid");
	}

    function __getBlockedUserFrontpage(){
	return $this->BlockedUserFrontpage->findAll(null,'BlockedUserFrontpage.user_id');
	}
	
	function __getFeaturedProjects($exclude_user_ids) { 
        $condition = '`projects`.`id` = `featured_projects`.`project_id`';
        $projects = $this->Project->getTopProjects('`featured_projects`.`id` `featured`',
                    '`featured` DESC', null, null, $exclude_user_ids,
                    NUM_FEATURED_PROJECTS,
                    $condition, '`featured_projects`');
		if(SHOW_RIBBON == 1){
			$projects = $this->set_ribbon($projects);
		}
        return $projects;
    }

    function __getTopViewedProjects($exclude_project_ids, $exclude_user_ids, $country = null) {
        if ($this->getContentStatus() == 'safe') {
		    $days = TOP_VIEWED_DAY_INTERVAL_SAFE;
        } else {
		    $days = TOP_VIEWED_DAY_INTERVAL;
        }
        $num_script = NUM_MIN_SCRIPT_FOR_TOP_VIWED;
		$condition = "totalScripts >= $num_script AND totalScripts IS NOT NULL";
		if($country){
			$condition = "totalScripts >= $num_script AND totalScripts IS NOT NULL AND `projects`.`country`= '$country'";
		}
		$topviewedProjects = $this->Project->getTopProjects('`views`', '`views` DESC', $days,
            $exclude_project_ids, $exclude_user_ids, NUM_TOP_VIEWED, $condition);
		if(SHOW_RIBBON ==1){
			$topviewedProjects = $this->set_ribbon($topviewedProjects);
		}
		return $topviewedProjects;
    }

    function __getTopRemixedProjects($exclude_project_ids, $exclude_user_ids, $country = null) {
        if ($this->getContentStatus() =='safe') {
		    $days = TOP_REMIXED_DAY_INTERVAL_SAFE;
        } else {
		    $days = TOP_REMIXED_DAY_INTERVAL;
        }
		$num_script = NUM_MIN_SCRIPT_FOR_TOP_REMIX;
		$condition = "remixer > 0 AND totalScripts >= $num_script AND totalScripts IS NOT NULL";
		if($country){
			$condition = "remixer > 0 AND totalScripts >= $num_script AND totalScripts IS NOT NULL AND `projects`.`country`= '$country'";
		}
        $topRemixedProjects =  $this->Project->getTopProjects('`remixer`', '`remixer` DESC', $days,
            $exclude_project_ids, $exclude_user_ids, NUM_TOP_REMIXED, $condition);
		if(SHOW_RIBBON ==1){
			$topRemixedProjects = $this->set_ribbon($topRemixedProjects);
		}
		return $topRemixedProjects;
    }

    function __getTopLovedProjects($exclude_project_ids, $exclude_user_ids, $country = null) {
        $num_script = NUM_MIN_SCRIPT_FOR_TOP_LOVED;
		$condition = "totalScripts >= $num_script AND totalScripts IS NOT NULL";
		if($country){
			$condition = "totalScripts >= $num_script AND totalScripts IS NOT NULL AND `projects`.`country`= '$country'";
		}
		$topLovedProjects =  $this->Project->getTopProjects('`loveitsuniqueip`', '`loveitsuniqueip` DESC', TOP_LOVED_DAY_INTERVAL,
            $exclude_project_ids, $exclude_user_ids, NUM_TOP_RATED, $condition);
		if(SHOW_RIBBON ==1){
			$topLovedProjects = $this->set_ribbon($topLovedProjects);
		}
		return $topLovedProjects;
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
		$days = TOP_DOWNLOAD_DAY_INTERVAL;
		$limits = NUM_TOP_DOWNLOAD;
        $topdpids =  $this->Project->query("SELECT project_id, COUNT(*) FROM `downloaders`,`projects` WHERE projects.created > now()  - interval $days day AND projects.id = downloaders.project_id AND proj_visibility = 'visible' $exclude_clause $exclude_user_id_clause $onlysafesql GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT $limits");
		$sqlor = "Project.id = " . (isset($topdpids[0]['downloaders']['project_id'])?$topdpids[0]['downloaders']['project_id']:-1) . " OR ";
		$sqlor .= "Project.id = " . (isset($topdpids[1]['downloaders']['project_id'])?$topdpids[1]['downloaders']['project_id']:-1) . " OR ";
		$sqlor .= "Project.id = " . (isset($topdpids[2]['downloaders']['project_id'])?$topdpids[2]['downloaders']['project_id']:-1);		

        $this->Project->unbindModel(
                array('hasMany' => array('GalleryProject'))
            );
        return $this->Project->findAll($sqlor . ' GROUP BY Project.user_id', NULL, NULL, $limits, 1, NULL, $this->getContentStatus());
    }

	/* Selects NUM_TOP_RATED random projects, consecutively */	
	// Avoid using MySQL's random due to bad performance
	function __getTopRandomProjects() {
        $this->Project->bindUser();
        $result = $this->Project->query("SELECT MAX(projects.id) AS maxid FROM projects");
	# To avoid full table scans which can occur due to Project.id being anywhere between 1 and MAX
	# we'll use a range ie. random to random + 500. 
        $random = rand(1, $result[0][0]['maxid']);
	$randomplus = $random + 500;
        $query = "Project.id BETWEEN $random AND $randomplus AND Project.status <> 'notsafe' AND Project.proj_visibility = 'visible'";

        $count = $this->Project->findCount($query);
        if ($count < NUM_TOP_RATED) { 
		$randomminus = $random - 500;
		$query = "Project.id BETWEEN $randomminus AND " . ($random+$count);
	}

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

    function __getTotalVisibleProjects() {
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  "Project.status = 'safe'";
	    }
        $this->Project->recursive = -1;
        $resultset =  $this->Project->findCount($onlysafesql); 
	 	return number_format(0.0 + $resultset);
    }
	
	function __getTotalProjects() {
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  "WHERE Project.status = 'safe'";
	    }
        $this->Project->recursive = -1;
        //$resultset =  $this->Project->findCount($onlysafesql);
		$resultset =  $this->Project->query("SELECT COUNT(*) AS `count` FROM `projects` $onlysafesql");
        return number_format(0.0 + $resultset['0']['0']['count']);
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
        $curator = $this->Curator->find(array('visibility'=>'visible'), array(), 'Curator.id DESC');
        $curator_id  = $curator['Curator']['user_id'];

        if(empty($curator_id)) { return null; }

        $condition = '`favorites`.`user_id` = '. $curator_id
                    .' AND `projects`.`user_id` <> '. $curator_id
                    .' AND `projects`.`id` = `favorites`.`project_id`';
        $projects = $this->Project->getTopProjects('`favorites`.`timestamp` `recency`',
                    '`recency` DESC', null, $exclude_project_ids, $exclude_user_ids,
                    NUM_CURATOR_FAV_PROJECT,
                    $condition, '`favorites`');
		if(SHOW_RIBBON ==1){
			$projects = $this->set_ribbon($projects);
		}

        return $projects;
	}
    
	function ___getCuratorName(){
    	$curator =$this->Curator->find(array('visibility'=>'visible'),array(),'Curator.id DESC');
        return $curator['User']['urlname'];
	}
	
    function __getDesignStudioProjects($exclude_project_ids, $exclude_user_ids) {
		$clubbed_gallery = $this->__getScratchClub();
        $gallery_id = $clubbed_gallery['id'];
        
        if(empty($gallery_id)) { return null; }

        $condition = '`gallery_id` = '. $gallery_id
                        .' AND `projects`.`id` = `gallery_projects`.`project_id`';
        
        $projects = $this->Project->getTopProjects('', 'RAND()', null, $exclude_project_ids,
                    $exclude_user_ids, NUM_DESIGN_STUDIO_PROJECT,
                    $condition, '`gallery_projects`');

        if(SHOW_RIBBON ==1){
			$projects = $this->set_ribbon($projects);
		}
		
		return $projects;
	}//function	
	
	function set_ribbon($final_projects) {
		$i =0;
		$temp = array();
		foreach($final_projects as $final_project){
				$temp_project = $final_project;
				$current_project_id = $final_project['Project']['id'];
				$feature_projects = $this->FeaturedProject->find("FeaturedProject.project_id = $current_project_id");
				if ($feature_projects) {
					$text =$this->convertDate($feature_projects['FeaturedProject']['timestamp']);
			 		$image_name =$this->ribbonImageName($feature_projects['FeaturedProject']['timestamp']);
			 		$this->Thumb->generateThumb($ribbon_image='ribbon.gif',$text,$dir="small_ribbon",$image_name,$dimension='40x30',125,125);
					$temp_project['Project']['ribbon_name'] = $image_name ;
					
				} else {
					$temp_project['Project']['ribbon_name'] = '' ;
				}
				$temp[$i++] = $temp_project;
		}
		return $temp;
	}
}
?>
