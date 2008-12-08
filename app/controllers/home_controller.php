<?php
Class HomeController extends AppController {
    /**
     * Home Page Controller
     */
	var $pageTitle = "Scratch | Home | imagine, program, share";
    var $uses = array("Project","User","FeaturedProject", "ClubbedGallery","UserStat", "Gallery", "Theme", "FeaturedGallery", "Tag", "Notification");
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

        $newprojects = $memcache->get("$prefix-newprojects");
        if ( $newprojects == "" ) {
       	    $newprojectstmp = $this->__getNewProjects();
            $memcache->set("$prefix-newprojects", $newprojectstmp, false, 60) or die ("Failed to save data at the server");
            $this->set('newprojects', $newprojectstmp);
        } else {
            $this->set('newprojects', $newprojects);
        }

        $featured = $memcache->get("$prefix-featured");
        if ( $featured == "" ) {
       	    $featuredtmp = $this->__getFeaturedProjects();
            $memcache->set("$prefix-featured", $featuredtmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('featuredprojects', $featuredtmp);
        } else {
            $this->set('featuredprojects', $featured);
        }

        $toploved = $memcache->get("$prefix-toploved");
        if ( $toploved == "" ) {
       	    $toplovedtmp = $this->__getTopLovedProjects();
            $memcache->set("$prefix-toploved", $toplovedtmp, false, 600) or die ("Failed to save data at the server");
            $this->set('toploved', $toplovedtmp);
        } else {
            $this->set('toploved', $toploved);
	}

        $topremixed = $memcache->get("$prefix-topremixed");
        if ( $topremixed == "" ) {
       	    $topremixedtmp = $this->__getTopRemixedProjects();
            $memcache->set("$prefix-topremixed", $topremixedtmp, false, 600) or die ("Failed to save data at the server");
            $this->set('topremixed', $topremixedtmp);
        } else {
            $this->set('topremixed', $topremixed);
	}

        $toprandoms = $memcache->get("$prefix-toprandoms");
        if ( $toprandoms == "" ) {
       	    $toprandomstmp = $this->__getTopRandomProjects();
            $memcache->set("$prefix-toprandoms", $toprandomstmp, false, 300) or die ("Failed to save data at the server");
            $this->set('toprandoms', $toprandomstmp);
        } else {
            $this->set('toprandoms', $toprandoms);
	}

        $topdownloaded = $memcache->get("$prefix-topdownloaded");
        if ( $topdownloaded == "" ) {
       	    $topdownloadedtmp = $this->__getTopDownloadedProjects();
            $memcache->set("$prefix-topdownloaded", $topdownloadedtmp, false, 600) or die ("Failed to save data at the server");
            $this->set('topdownloaded', $topdownloadedtmp);
        } else {
            $this->set('topdownloaded', $topdownloaded);
	}

        $topviewed = $memcache->get("$prefix-topviewed");
        if ( $topviewed == "" ) {
       	    $topviewedtmp = $this->__getTopViewedProjects();
            $memcache->set("$prefix-topviewed", $topviewedtmp, false, 600) or die ("Failed to save data at the server");
            $this->set('topviewed', $topviewedtmp);
        } else {
            $this->set('topviewed', $topviewed);
	}

        $scratch_club = $memcache->get("$prefix-scratch_club");
        if ( $scratch_club == "" ) {
       	    $scratch_clubtmp = $this->__getScratchClub();
            $memcache->set("$prefix-scratch_club", $scratch_clubtmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('scratch_club', $scratch_clubtmp);
        } else {
            $this->set('scratch_club', $scratch_club);
	}

        $featuredthemes = $memcache->get("$prefix-featuredthemes");
        if ( $featuredthemes == "" ) {
       	    $featuredthemestmp = $this->__getFeaturedGalleries();
            $memcache->set("$prefix-featuredthemes", $featuredthemestmp, false, 3600) or die ("Failed to save data at the server");
            $this->set('featuredthemes', $featuredthemestmp);
        } else {
            $this->set('featuredthemes', $featuredthemes);
	}

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

    function __getFeaturedProjects() {
        $this->Project->bindUser();
        return $this->FeaturedProject->findAll(NULL, NULL, "FeaturedProject.id DESC", NUM_FEATURED_PROJECTS, NULL, 2);
    }

    function __getTopViewedProjects() {
        $this->Project->bindUser();
        if ($this->getContentStatus() =='safe') {
		    $days = 20;
        } else {
		    $days = 4;
        }
        return  $this->Project->findAll("Project.created > now() - interval $days  day AND Project.user_id > 0 AND Project.proj_visibility = 'visible' AND Project.status <> 'notsafe'", NULL, "Project.views DESC", NUM_TOP_VIEWED, 1, NULL, $this->getContentStatus());
    }

    function __getTopRemixedProjects() {
        $this->Project->bindUser();
        if ($this->getContentStatus() =='safe') {
		    $days = 20;
        } else {
		    $days = 10;
	}
        return $this->Project->findAll("Project.created > now() - interval $days  day AND Project.user_id > 0 AND Project.proj_visibility = 'visible' AND Project.remixes > 0 AND Project.status <> 'notsafe'", NULL, "Project.remixes DESC", NUM_TOP_REMIXED, 1, NULL, $this->getContentStatus());
    }

    function __getTopLovedProjects() {
        $this->Project->bindUser();
        return $this->Project->findAll("Project.created > now() - interval 10 day AND  Project.proj_visibility = 'visible' AND Project.status <> 'notsafe'", NULL, "Project.loveit DESC", NUM_TOP_RATED, 1, NULL, $this->getContentStatus());
    }

    function __getTopDownloadedProjects() {
        $this->Project->bindUser();
        $onlysafesql = '';
        if ($this->getContentStatus() == 'safe') {
            $onlysafesql =  "AND projects.status = 'safe'";
        }
        else {
            $onlysafesql =  "AND projects.status <> 'notsafe'";
        }
        $topdpids =  $this->Project->query("SELECT project_id, COUNT(*) FROM `downloaders`,`projects` WHERE projects.created > now()  - interval 7 day AND projects.id = downloaders.project_id AND proj_visibility = 'visible' $onlysafesql GROUP BY project_id ORDER BY COUNT(*) DESC LIMIT 3");
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
        $query = "Project.id >= $random AND Project.status <> 'notsafe'";
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
}
?>

