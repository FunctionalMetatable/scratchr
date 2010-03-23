<?php
Class TaggedController  extends AppController {

     var $uses = array('IgnoredUser', 'Project','ProjectTag', 'Tag', 'Gallery', 'GalleryTag', 'Notification', 'FeaturedProject');
    var $helpers = array('Pagination');
    var $layout = 'scratchr_default';

 function shared($tag_name, $option = "recent") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
		$this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$tag_id = $tag['Tag']['id'];
		$days = LATEST_SHARED_DAY_INTERVAL;
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND Project.created > now()  - interval $days day AND ProjectTag.tag_id = $tag_id GROUP BY project_id";

        if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
		}
		
		$order = 'Project.created DESC';
		
		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$limit = NUM_SHARED_TAGED_PROJECT;
		$ttl = TAGED_SHARED_PROJECT_CACHE_TTL; 
		$mc_key = 'project_tag_'.$tag_id.'_'.$content_status.'_'. $option.'_'. $limit.'_'. $ttl;
		
        $this->ProjectTag->mc_connect();
        $tag_projects = $this->ProjectTag->mc_get($mc_key);
        if($tag_projects === false) {
			$this->Project->unbindModel(array('hasMany' => array('GalleryProject')));
			$this->ProjectTag->unbindModel(array('belongsTo' => array('User')));
			$this->ProjectTag->recursive = 2;	
			$tag_projects = $this->ProjectTag->findAll($final_criteria, NULL , $order, NUM_SHARED_TAGED_PROJECT);
			$tag_projects = $this->set_projects($tag_projects);
			$this->ProjectTag->mc_set($mc_key, $tag_projects, false, $ttl);
		}
		$this->ProjectTag->mc_close();
		$this->set('option', $option);
        $this->set('tag_projects', $tag_projects);
		$this->set('tag_name', $tag_name);
        $this->set('tag', $tag);
        $this->render('projects');
    }
	
	 function remixed($tag_name, $option = "remixed") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
		$this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$tag_id = $tag['Tag']['id'];
		$days = LATEST_RAMIXED_DAY_INTERVAL;
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND Project.created > now()  - interval $days day AND ProjectTag.tag_id = $tag_id GROUP BY project_id";
        if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
		}
		
		$order = 'Project.remixer DESC';
	
		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$limit = NUM_RAMIXED_TAGED_PROJECT;
		$ttl = TAGED_RAMIXED_PROJECT_CACHE_TTL; 
		$mc_key = 'project_tag_'.$tag_id.'_'.$content_status.'_'. $option.'_'. $limit.'_'. $ttl;
		
        $this->ProjectTag->mc_connect();
        $tag_projects = $this->ProjectTag->mc_get($mc_key);
        if($tag_projects === false) {
			$this->Project->unbindModel(array('hasMany' => array('GalleryProject')));
			$this->ProjectTag->unbindModel(array('belongsTo' => array('User')));
			$this->ProjectTag->recursive = 2;	
			$tag_projects = $this->ProjectTag->findAll($final_criteria, NULL , $order, NUM_RAMIXED_TAGED_PROJECT);
			$tag_projects = $this->set_projects($tag_projects);
			$this->ProjectTag->mc_set($mc_key, $tag_projects, false, $ttl);
		}
		$this->ProjectTag->mc_close();
		$this->set('option', $option);
        $this->set('tag_projects', $tag_projects);
		$this->set('tag_name', $tag_name);
        $this->set('tag', $tag);
        $this->render('projects');
    }
	
	 function topviewed($tag_name, $option = "topviewed") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
		$this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$tag_id = $tag['Tag']['id'];
		$days = LATEST_TOPVIEWED_DAY_INTERVAL;
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND Project.created > now()  - interval $days day AND ProjectTag.tag_id = $tag_id GROUP BY project_id";

        if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
		}
		
		$order = 'Project.views DESC';
	
		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$limit = NUM_TOPVIEWED_TAGED_PROJECT;
		$ttl = TAGED_TOPVIEWED_PROJECT_CACHE_TTL; 
		$mc_key = 'project_tag_'.$tag_id.'_'.$content_status.'_'. $option.'_'. $limit.'_'. $ttl;
		
        $this->ProjectTag->mc_connect();
        $tag_projects = $this->ProjectTag->mc_get($mc_key);
        if($tag_projects === false) {
			$this->Project->unbindModel(array('hasMany' => array('GalleryProject')));
			$this->ProjectTag->unbindModel(array('belongsTo' => array('User')));
			$this->ProjectTag->recursive = 2;	
			$tag_projects = $this->ProjectTag->findAll($final_criteria, NULL , $order, NUM_TOPVIEWED_TAGED_PROJECT);
			$tag_projects = $this->set_projects($tag_projects);
			$this->ProjectTag->mc_set($mc_key, $tag_projects, false, $ttl);
		}
		$this->ProjectTag->mc_close();
		$this->set('option', $option);
        $this->set('tag_projects', $tag_projects);
		$this->set('tag_name', $tag_name);
        $this->set('tag', $tag);
        $this->render('projects');
    }
	
	 function toploved($tag_name, $option = "toploved") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
		$this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$tag_id = $tag['Tag']['id'];
		$days = LATEST_TOPLOVED_DAY_INTERVAL;
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND Project.created > now()  - interval $days day AND ProjectTag.tag_id = $tag_id GROUP BY project_id";

        if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
		}
		
		$order = 'Project.loveitsuniqueip DESC';

		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$limit = NUM_TOPLOVED_TAGED_PROJECT;
		$ttl = TAGED_TOPLOVED_PROJECT_CACHE_TTL; 
		$mc_key = 'project_tag_'.$tag_id.'_'.$content_status.'_'. $option.'_'. $limit.'_'. $ttl;
		
        $this->ProjectTag->mc_connect();
        $tag_projects = $this->ProjectTag->mc_get($mc_key);
        if($tag_projects === false) {
			$this->Project->unbindModel(array('hasMany' => array('GalleryProject')));
			$this->ProjectTag->unbindModel(array('belongsTo' => array('User')));
			$this->ProjectTag->recursive = 2;	
			$tag_projects = $this->ProjectTag->findAll($final_criteria, NULL , $order, NUM_TOPLOVED_TAGED_PROJECT);
			$tag_projects = $this->set_projects($tag_projects);
			$this->ProjectTag->mc_set($mc_key, $tag_projects, false, $ttl);
		}
		$this->ProjectTag->mc_close();
		$this->set('option', $option);
        $this->set('tag_projects', $tag_projects);
		$this->set('tag_name', $tag_name);
        $this->set('tag', $tag);
        $this->render('projects');
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
			//Ribbon feature
			$image_name ='';
			if(SHOW_RIBBON ==1){
				$featured_time = $this->FeaturedProject->field('timestamp',array('project_id'=>$project['Project']['id']));
				if(!empty($featured_time)){
					$text =$this->convertDate($featured_time);
					$image_name =$this->ribbonImageName($featured_time );
					$this->Thumb->generateThumb($ribbon_image='ribbon.gif',$text,$dir="small_ribbon",$image_name,$dimension='40x30',125,125);	
				}
			}
			$temp_project['Project']['ribbon_name'] = $image_name;	
			array_push($return_projects, $temp_project);
		}
		return $return_projects;
	}
	

}
