<?php
Class HomeController extends AppController {
    /**
     * Home Page Controller
     */
	var $pageTitle = "Scratch | Home | imagine, program, share";
    var $uses = array("Project","User","FeaturedProject", "ClubbedGallery","UserStat", "Gallery", "Theme", "FeaturedGallery", "Tag", "Notification","Curator","Favorite");
    var $helpers = array("Tagcloud", 'Cache');
    var $cacheAction = "1 hour";
	
	/**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }
	
    function index() {
		$this->cacheAction = array("1 hour");
		$this->__setNewProjects();
		$this->__setFeaturedProjects();
		$this->__setTopRatedProjects();
		$this->__setTopViewedProjects();
		$this->__setTopRemixedProjects();
		$this->__setTopRandomProjects();
		$this->__setNewMembers();
		$this->__setRecentVisitors();
		$this->__setFeaturedGalleries();
		// commented out due to performance problems $this->__setTagCloud();
		$this->__setScratchClub();
		$this->__setTotalProjects();
		$this->__setTotalUsers();
		$this->__setTopDownloadedProjects();
		$this->__setTotalScripts();
		$this->__setTotalSprites();
		$this->__setTotalCreators();
		$this->__getTopCountries();
		$this->__getCuratorFevorites();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$newest_feed_link = "/feeds/getNewestProjects";
		$featured_feed_link = "/feeds/getFeaturedProjects";
		
		$this->set('newest_feed_link', $newest_feed_link);
		$this->set('featured_feed_link', $featured_feed_link);
		$this->set('ishomepage', true);
    }


	function __setScratchClub() {
        $club = $this->ClubbedGallery->find(NULL, NULL, "ClubbedGallery.id DESC");
		$club = $this->finalize_gallery($club);
        $this->set('scratch_club', $club['Gallery']);
	}
	
	
    function __setNewProjects() {
        $this->Project->bindUser();
        $newprojects = $this->Project->findAll("Project.proj_visibility = 'visible'", null, "Project.created DESC", NUM_NEW_PROJECTS, 1, null, $this->getContentStatus());
        $this->set('newprojects', $newprojects);
    }

    function __setFeaturedProjects() {
        $this->Project->bindUser();
        $featured = $this->FeaturedProject->findAll(NULL, NULL, "FeaturedProject.id DESC", NUM_FEATURED_PROJECTS, NULL, 2);
        $this->set('featuredprojects', $featured);
    }

    function __setTopViewedProjects() {
        $this->Project->bindUser();
	if ($this->getContentStatus() =='safe') {
		$days = 20;
	} else {
		$days = 3;
	}
        $topviewed = $this->Project->findAll("Project.created > now() - interval $days  day AND Project.user_id > 0 AND Project.proj_visibility = 'visible'", NULL, "Project.views DESC", NUM_TOP_VIEWED, 1, NULL, $this->getContentStatus());
        $this->set('topviewed', $topviewed);
    }

    function __setTopRemixedProjects() {
        $this->Project->bindUser();
	if ($this->getContentStatus() =='safe') {
		$days = 20;
	} else {
		$days = 20;
	}
        $topremixed = $this->Project->findAll("Project.created > now() - interval $days  day AND Project.user_id > 0 AND Project.proj_visibility = 'visible' AND Project.remixes > 0 AND Project.status <> 'notsafe'", NULL, "Project.remixes DESC", NUM_TOP_REMIXED, 1, NULL, $this->getContentStatus());
        $this->set('topremixed', $topremixed);
    }

    function __setTopRatedProjects() {
        $this->Project->bindUser();
        $toploved = $this->Project->findAll("Project.created > now() - interval 5 day AND  Project.proj_visibility = 'visible' AND Project.status <> 'notsafe'", NULL, "Project.loveit DESC", NUM_TOP_RATED, 1, NULL, $this->getContentStatus());
        $this->set('toploved', $toploved);
    }

    function __setTopDownloadedProjects() {
        $this->Project->bindUser();
	$onlysafesql = '';
	if ($this->getContentStatus() == 'safe') {
		$onlysafesql =  "AND projects.status = 'safe'";
	}
	else {
		$onlysafesql =  "AND projects.status <> 'notsafe'";
	}
        $topdpids =  $this->Project->query("SELECT project_id, COUNT(*) FROM `downloaders`,`projects` WHERE projects.created > now()  - interval 10 day AND projects.id = downloaders.project_id AND proj_visibility = 'visible' $onlysafesql GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT 3");
		$sqlor = "Project.id = " . (isset($topdpids[0]['downloaders']['project_id'])?$topdpids[0]['downloaders']['project_id']:-1) . " OR ";
		$sqlor .= "Project.id = " . (isset($topdpids[1]['downloaders']['project_id'])?$topdpids[1]['downloaders']['project_id']:-1) . " OR ";
		$sqlor .= "Project.id = " . (isset($topdpids[2]['downloaders']['project_id'])?$topdpids[2]['downloaders']['project_id']:-1);
		
		$topdownloaded = $this->Project->findAll($sqlor, NULL, NULL, 3, 1, NULL, $this->getContentStatus());
        $this->set('topdownloaded', $topdownloaded);
    }

	/* Selects NUM_TOP_RATED random projects, consecutively */	
	// Avoid using MySQL's random due to bad performance
	function __setTopRandomProjects() {
        $this->Project->bindUser();
        $result = $this->Project->query("SELECT MAX(projects.id) AS maxid FROM projects");
	$random = rand(1, $result[0][0]['maxid']);
	$query = "Project.id >= $random";
	$count = $this->Project->findCount($query);
	if ($count < NUM_TOP_RATED) $query = "Project.id <= ".($random+$count);
	$toprandoms = $this->Project->findAll($query, NULL, "Project.id", NUM_TOP_RATED, 1, NULL, $this->getContentStatus());
	$this->set('toprandoms', $toprandoms);
    }

    function __setNewMembers() {
        $newmembers = $this->User->findAll(NULL, NULL, "User.created DESC", NUM_NEW_MEMBERS);
        $this->set('newmembers', $newmembers);
    }

    function __setRecentVisitors() {
        $this->set('recentvisitors',$this->UserStat->findAll(NULL, NULL, "UserStat.lastin DESC", NUM_RECENT_VISITORS));
    }

    function __setFeaturedGalleries() {
        $featuredgalleries = $this->FeaturedGallery->findAll(NULL, NULL, "FeaturedGallery.id DESC", NUM_FEATURED_THEMES);
		$featuredgalleries = $this->finalize_galleries($featuredgalleries);
        $this->set('featuredthemes', $featuredgalleries);
    }

    function __setTagCloud() {
        $resultset = $this->Tag->getProjectTagCloud(TAG_CLOUD_HOME);
        $this->set('tags', $resultset);
    }

    function __setTotalProjects() {
	$onlysafesql = '';
	if ($this->getContentStatus() == 'safe') {
		$onlysafesql =  "Project.status = 'safe'";
	}
        $resultset = $this->Project->findCount($onlysafesql);
        $this->set('totalprojects', number_format(0.0 + $resultset));
    }

    function __setTotalProjects7days() {
        $resultset = $this->Project->findCount("");
        $this->set('totalprojects', number_format(0.0 + $resultset));
    }

    function __setTotalUsers() {
        $resultset = $this->User->findCount("villager=0");
        $this->set('totalusers', number_format(0.0 + $resultset));
    }
		
    function __setTotalUsers7days() {
        $resultset = $this->Users->findCount("1=1");
        $this->set('totalusers', $resultset);
    }
    function __setTotalScripts() {
	$onlysafesql = '';
	if ($this->getContentStatus() == 'safe') {
		$onlysafesql =  "WHERE projects.status = 'safe'";
	}
	$resultset = $this->Project->query("select sum(totalScripts) as totalscripts from projects $onlysafesql");
	$this->set('totalscripts', number_format(0.0 + $resultset[0][0]['totalscripts']));
     }

    function __setTotalSprites() {
	$onlysafesql = '';
	if ($this->getContentStatus() == 'safe') {
		$onlysafesql =  "WHERE projects.status = 'safe'";
	}
	$resultset = $this->Project->query("select sum(numberOfSprites) as totalsprites from projects $onlysafesql");
	$this->set('totalsprites', number_format(0.0 + $resultset[0][0]['totalsprites']));
    }

    function __setTotalCreators() {
        $resultset = $this->Project->query("select count(distinct(user_id)) as totalcreators from projects where user_id");
        $this->set('totalcreators', number_format(0.0 + $resultset[0][0]['totalcreators']));
    }
	function __getTopCountries(){
	$countries = $this->User->query("SELECT count(*)as cnt, country FROM `users` group by country order by cnt desc  LIMIT ".NUM_TOP_COUNTRIES);
	$this->set('countries',$countries);
	}
	function __getCuratorFevorites(){
	$favorites =array();
	 $curator =$this->Curator->find(null,array(),'Curator.id DESC');
	 $curator_id =$curator['Curator']['user_id'];
	 if($curator_id)
	$favorites = $this->Favorite->findAll("Favorite.user_id= $curator_id AND Project.proj_visibility = 'visible' AND Project.user_id <>$curator_id", null, 'Favorite.timestamp DESC', 3 ,null,2);
	$this->set('username',$curator['User']['urlname']);
	$this->set('favorites',$favorites);	
	
	}
}
?>
