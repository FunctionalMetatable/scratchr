<?php
Class HomeController extends AppController {
    /**
     * Home Page Controller
     */
	var $pageTitle = "Scratch | Home | imagine, program, share";
    var $uses = array("Project","User","FeaturedProject", "ClubbedGallery","UserStat", "Gallery", "Theme", "FeaturedGallery", "Tag", "Notification","Curator","Favorite", "BlockedUserFrontpage");
    var $helpers = array("Tagcloud");
   	
	/**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
    function index() {
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211) or die ("Could not connect");
        $prefix = MEMCACHE_PREFIX;
        $project_ids = array();
       	$user_ids =array();
        $home_projects = $memcache->get("$prefix-home_projects");
        if(!$home_projects) {
			$curator_name = $this->___getCuratorName();
			
			$frontpage_blocked_user = $this->__getBlockedUserFrontpage();
			$frontpage_blocked_user_id = Set::extract('/BlockedUserFrontpage/user_id', $frontpage_blocked_user);
			$user_ids       =array_merge($user_ids, $frontpage_blocked_user_id);
			
			$favorites = $this->__getCuratorFavorites($user_ids);
			$curator_favorites_id = Set::extract('/Project/id', $favorites);
			$this->Project->register_frontpage($curator_favorites_id, 'curator_favorites');
			$project_ids    = array_merge($project_ids, $curator_favorites_id);
		
            $featured       = $this->__getFeaturedProjects($project_ids, $user_ids);
            $featured_ids   = Set::extract('/FeaturedProject/project_id', $featured);
			$featured_project_owner_ids   = Set::extract('/Project/user_id', $featured);
            $this->Project->register_frontpage($featured_ids, 'featured');
            $project_ids    = array_merge($project_ids, $featured_ids);
            $user_ids       =array_merge($user_ids, $featured_project_owner_ids);
			
            $topremixed     = $this->__getTopRemixedProjects($project_ids, $user_ids);
            $topremixed_ids = Set::extract('/Project/id', $topremixed);
			$topremixed_user_ids = Set::extract('/Project/user_id', $topremixed);
            $this->Project->register_frontpage($topremixed_ids, 'top_remixed');
            $project_ids    = array_merge($project_ids, $topremixed_ids);
			$user_ids       =array_merge($user_ids, $topremixed_user_ids);
			
            $toploved       = $this->__getTopLovedProjects($project_ids, $user_ids);
            $toploved_ids   = Set::extract('/Project/id', $toploved);
			$toploved_user_ids   = Set::extract('/Project/user_id', $toploved);
            $this->Project->register_frontpage($toploved_ids, 'top_loved');
            $project_ids    = array_merge($project_ids, $toploved_ids);
			$user_ids       =array_merge($user_ids, $toploved_user_ids);
			
            $topviewed      = $this->__getTopViewedProjects($project_ids, $user_ids);
            $topviewed_ids  = Set::extract('/Project/id', $topviewed);
			$topviewed_user_ids  = Set::extract('/Project/id', $topviewed);
            $this->Project->register_frontpage($topviewed_ids, 'top_viewed');
            $project_ids    = array_merge($project_ids, $topviewed_ids);
			$user_ids       =array_merge($user_ids, $topviewed_user_ids);

            $topdownloaded  = $this->__getTopDownloadedProjects($project_ids, $user_ids);
            $topdownloaded_ids  = Set::extract('/Project/id', $topdownloaded);
            $this->Project->register_frontpage($topdownloaded_ids, 'top_downloaded');
		 
			$home_projects = array('featured' => $featured,
                                   'topremixed' => $topremixed,
                                   'toploved' => $toploved,
                                   'topviewed' => $topviewed,
                                   'topdownloaded' => $topdownloaded,
								   'favorites' => $favorites,
								   'curator_name' =>$curator_name
                                );
                                
            $memcache->set("$prefix-home_projects", $home_projects, false, 3600) or die ("Failed to save data at the server");
        }
        else {
            $featured       = $home_projects['featured'];
            $topremixed     = $home_projects['topremixed'];
            $toploved       = $home_projects['toploved'];
            $topviewed      = $home_projects['topviewed'];
            $topdownloaded  = $home_projects['topdownloaded'];
			$favorites      = $home_projects['favorites'];
			$curator_name   = $home_projects['curator_name'];
        }
		
        $this->set('featuredprojects', $featured);
        $this->set('topremixed', $topremixed);
        $this->set('toploved', $toploved);
        $this->set('topviewed', $topviewed);
        $this->set('topdownloaded', $topdownloaded);
		$this->set('favorites', $favorites);
		$this->set('username',$curator_name);

        $newprojects = $memcache->get("$prefix-newprojects");
        if ( $newprojects == "" ) {
       	    $newprojectstmp = $this->__getNewProjects();
            $memcache->set("$prefix-newprojects", $newprojectstmp, false, 60) or die ("Failed to save data at the server");
            $this->set('newprojects', $newprojectstmp);
        } else {
            $this->set('newprojects', $newprojects);
        }

        $toprandoms = $memcache->get("$prefix-toprandoms");
        if ( !$toprandoms) {
       	    $toprandoms = $this->__getTopRandomProjects();
            $memcache->set("$prefix-toprandoms", $toprandoms, false, 300) or die ("Failed to save data at the server");
            $toprandoms_ids   = Set::extract('/Project/id', $toprandoms);
            $this->Project->register_frontpage($toprandoms_ids, 'surprise');
            $this->set('toprandoms', $toprandoms);
        }
        $this->set('toprandoms', $toprandoms);
        
        $scratch_club = $memcache->get("$prefix-scratch_club");
        if ( $scratch_club == "" ) {
       	    $scratch_clubtmp = $this->__getScratchClub();
            $memcache->set("$prefix-scratch_club", $scratch_clubtmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('scratch_club', $scratch_clubtmp);
        } else {
            $this->set('scratch_club', $scratch_club);
        }

        $featuredthemes = false;//$memcache->get("$prefix-featuredthemes");
        if ( !$featuredthemes ) {
       	    $featuredthemes = $this->__getFeaturedGalleries();
            $featuredthemes_ids   = Set::extract('/Gallery/id', $featuredthemes);
            $this->Gallery->register_frontpage($featuredthemes_ids, 'featured');
            $memcache->set("$prefix-featuredthemes", $featuredthemes, false, 3600) or die ("Failed to save data at the server");
        }
        $this->set('featuredthemes', $featuredthemes);
        
        $recentvisitors = $memcache->get("$prefix-recentvisitors");
        if ( $recentvisitors == "" ) {
       	    $recentvisitorstmp = $this->__getRecentVisitors();
            $memcache->set("$prefix-recentvisitors", $recentvisitorstmp, false, 600) or die ("Failed to save data at the server");
            $this->set('recentvisitors', $recentvisitorstmp);
        } else {
            $this->set('recentvisitors', $recentvisitors);
        }

        $newmembers = $memcache->get("$prefix-newmembers");
        if ( $newmembers == "" ) {
       	    $newmemberstmp = $this->__getNewMembers();
            $memcache->set("$prefix-newmembers", $newmemberstmp, false, 600) or die ("Failed to save data at the server");
            $this->set('newmembers', $newmemberstmp);
        } else {
            $this->set('newmembers', $newmembers);
        }

        $totalprojects = $memcache->get("$prefix-totalprojects");
        if ( $totalprojects == "" ) {
       	    $totalprojectstmp = $this->__getTotalProjects();
            $memcache->set("$prefix-totalprojects", $totalprojectstmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('totalprojects', $totalprojectstmp);
        } else {
            $this->set('totalprojects', $totalprojects);
        }

        $totalscripts = $memcache->get("$prefix-totalscripts");
        if ( $totalscripts == "" ) {
       	    $totalscriptstmp = $this->__getTotalScripts();
            $memcache->set("$prefix-totalscripts", $totalscriptstmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('totalscripts', $totalscriptstmp);
        } else {
            $this->set('totalscripts', $totalscripts);
        }

        $totalsprites = $memcache->get("$prefix-totalsprites");
        if ( $totalsprites == "" ) {
       	    $totalspritestmp = $this->__getTotalSprites();
            $memcache->set("$prefix-totalsprites", $totalspritestmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('totalsprites', $totalspritestmp);
        } else {
            $this->set('totalsprites', $totalsprites);
        }

        $totalcreators = $memcache->get("$prefix-totalcreators");
        if ( $totalcreators == "" ) {
       	    $totalcreatorstmp = $this->__getTotalCreators();
            $memcache->set("$prefix-totalcreators", $totalcreatorstmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('totalcreators', $totalcreatorstmp);
        } else {
            $this->set('totalcreators', $totalcreators);
        }

        $totalusers = $memcache->get("$prefix-totalusers");
        if ( $totalusers == "" ) {
       	    $totaluserstmp = $this->__getTotalUsers();
            $memcache->set("$prefix-totalusers", $totaluserstmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('totalusers', $totaluserstmp);
        } else {
            $this->set('totalusers', $totalusers);
        }

        $tags = $memcache->get("$prefix-tags");
        if ( $tags == "" ) {
       	    $tagstmp = $this->__getTagCloud();
            $memcache->set("$prefix-tags", $tagstmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('tags', $tagstmp);
        } else {
            $this->set('tags', $tags);
        }
		
		$countries = $memcache->get("$prefix-countries");
        if ( $countries == "" ) {
       	    $countriestmp = $this->__getTopCountries();
            $memcache->set("$prefix-countries", $countriestmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('countries', $countriestmp);
        } else {
            $this->set('countries', $countries);
        }
		
		/*$favorites = $memcache->get("$prefix-favorites");
		$curator_name = $memcache->get("$prefix-curator_name");
        if ( $favorites == "" || $curator_name =="") {
       	    $curator_favorites = $this->__getCuratorFavorites();
			$curator_name = $this->___getCuratorName();
            $memcache->set("$prefix-favorites", $curator_favorites, false, 3600) or die ("Failed to save data at the server");
			$memcache->set("$prefix-curator_name", $curator_name, false, 3600) or die ("Failed to save data at the server");
            $this->set('fevorites', $curator_favorites);
			$this->set('username',$curator_name);
        } else {
            $this->set('favorites', $favorites);
			$this->set('username',$curator_name);
        }*/

    	$memcache->close();
		
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
		$club = $this->finalize_gallery($club);
        return  $club['Gallery']; 
	}
	
    function __getNewProjects() {
        $this->Project->bindUser();
        return $this->Project->findAll("Project.proj_visibility = 'visible' AND Project.status <> 'notsafe'", null, "Project.created DESC", NUM_NEW_PROJECTS, 1, null, $this->getContentStatus());
    }

    function __getBlockedUserFrontpage(){
	return $this->BlockedUserFrontpage->findAll(null,'BlockedUserFrontpage.user_id');
	}
	
	function __getFeaturedProjects($exclude_project_ids, $exclude_user_ids) {
	$exclude_clause = '';
	$exclude_user_id_clause = '';
	 if(!empty($exclude_project_ids)) {
            $exclude_clause = ' AND FeaturedProject.project_id NOT IN ( '.implode($exclude_project_ids, ' , ').' )';
        }
	if(!empty($exclude_user_ids)) {
            $exclude_user_id_clause = ' AND Project.user_id NOT IN ( '.implode($exclude_user_ids, ' , ').' )';
        }	
        $this->Project->bindUser();
        return $this->FeaturedProject->findAll("Project.proj_visibility = 'visible'".$exclude_clause.$exclude_user_id_clause, NULL, "FeaturedProject.id DESC", NUM_FEATURED_PROJECTS, NULL, 2);
    }

    function __getTopViewedProjects($exclude_project_ids, $exclude_user_ids) {
        $exclude_clause = '';
		$exclude_user_id_clause = '';
        if(!empty($exclude_project_ids)) {
            $exclude_clause = ' AND Project.id NOT IN ( '.implode($exclude_project_ids, ' , ').' )';
        }
		if(!empty($exclude_user_ids)) {
            $exclude_user_id_clause = ' AND Project.user_id NOT IN ( '.implode($exclude_user_ids, ' , ').' )';
        }
        $this->Project->bindUser();
        if ($this->getContentStatus() =='safe') {
		    $days = 20;
        } else {
		    $days = 4;
        }
        return  $this->Project->findAll("Project.created > now() - interval $days  day AND Project.user_id > 0 AND Project.proj_visibility = 'visible' AND Project.status <> 'notsafe'".$exclude_clause.$exclude_user_id_clause, NULL, "Project.views DESC", NUM_TOP_VIEWED, 1, NULL, $this->getContentStatus());
    }

    function __getTopRemixedProjects($exclude_project_ids, $exclude_user_ids) {
        $exclude_clause = '';
		$exclude_user_id_clause = '';
        if(!empty($exclude_project_ids)) {
            $exclude_clause = ' AND Project.id NOT IN ( '.implode($exclude_project_ids, ' , ').' )';
        }
		if(!empty($exclude_user_ids)) {
            $exclude_user_id_clause = ' AND Project.user_id NOT IN ( '.implode($exclude_user_ids, ' , ').' )';
        }
        $this->Project->bindUser();
        if ($this->getContentStatus() =='safe') {
		    $days = 20;
        } else {
		    $days = 10;
	}
        return $this->Project->findAll("Project.created > now() - interval $days  day AND Project.user_id > 0 AND Project.proj_visibility = 'visible' AND Project.remixer > 0 AND Project.status <> 'notsafe'".$exclude_clause.$exclude_user_id_clause, NULL, "Project.remixer DESC", NUM_TOP_REMIXED, 1, NULL, $this->getContentStatus());
    }

    function __getTopLovedProjects($exclude_project_ids, $exclude_user_ids) {
        $exclude_clause = '';
		$exclude_user_id_clause = '';
        if(!empty($exclude_project_ids)) {
            $exclude_clause = ' AND Project.id NOT IN ( '.implode($exclude_project_ids, ' , ').' )';
        }
		if(!empty($exclude_user_ids)) {
            $exclude_user_id_clause = ' AND Project.user_id NOT IN ( '.implode($exclude_user_ids, ' , ').' )';
        }
        $this->Project->bindUser();
        return $this->Project->findAll("Project.created > now() - interval 10 day AND  Project.proj_visibility = 'visible' AND Project.status <> 'notsafe'".$exclude_clause.$exclude_user_id_clause, NULL, "Project.loveit DESC", NUM_TOP_RATED, 1, NULL, $this->getContentStatus());
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
		return $this->Project->findAll($sqlor, NULL, NULL, 3, 1, NULL, $this->getContentStatus());
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
	
	function __getCuratorFavorites($exclude_user_ids){
	
	$exclude_user_id_clause = '';
       
		if(!empty($exclude_user_ids)) {
           $exclude_user_id_clause = ' AND Project.user_id NOT IN ( '.implode($exclude_user_ids, ' , ').' )';
        }

	
		$favorites =array();
		$curator =$this->Curator->find(null,array(),'Curator.id DESC');
	 	$curator_id =$curator['Curator']['user_id'];
		if($curator_id)
		$favorites = $this->Favorite->findAll("Favorite.user_id= $curator_id AND Project.proj_visibility = 'visible' AND Project.user_id <>$curator_id".$exclude_user_id_clause, null, 'Favorite.timestamp DESC', 3 ,null,2);
		return  $favorites;
	}
	function ___getCuratorName(){
	$curator =$this->Curator->find(null,array(),'Curator.id DESC');
	return $curator['User']['urlname'];
	}
}
?>