<?php
Class TagsController extends AppController {

    var $uses = array('IgnoredUser', 'Project','ProjectTag', 'Tag', 'Gallery', 'GalleryTag', 'Notification', 'FeaturedProject');
    var $helpers = array('Tagcloud','Pagination');
    var $components = array("Pagination");
    var $layout = 'scratchr_default';

    /**
     * Called before every controller action
	 * Overrides AppController::beforeFilter()
     */
    function beforeFilter() {
		$this->set('content_status', $this->getContentStatus());
    }

    function index() {
        $this->__setProjectTagCloud();
    }
	
    function browse_projects() {
		$this->__setProjectTagCloud();
    }
	
    function browse_galleries() {
		$this->__setGalleryTagCloud();
		
		$this->render('browse_galleries');
    }
	
    function __setProjectTagCloud() {

	# Check memcache first
        $mc_key = 'project_tag_cloud';
        $this->Tag->mc_connect();
        $resultset = $this->Tag->mc_get($mc_key);

        if($resultset === false) {

	    $resultset = $this->Tag->query("
	      SELECT Tag.name, COUNT( tt.project_id ) AS tagcounter 
	      FROM project_tags tt, tags Tag  
	      WHERE Tag.id = tt.tag_id  GROUP BY Tag.id  
	      ORDER BY tagcounter DESC LIMIT " . TAG_CLOUD_BIG);

	      $this->Tag->mc_set($mc_key, $resultset, false, TAG_CLOUD_TTL);

        }
        $this->Tag->mc_close();

        $this->set('tags', $resultset);

      }
	
     function __setGalleryTagCloud() {

	# Check memcache first
        $mc_key = 'gallery_tag_cloud';
        $this->Tag->mc_connect();
        $resultset = $this->Tag->mc_get($mc_key);

        if($resultset === false) {

	  $resultset = $this->Tag->query("
            SELECT Tag.name, COUNT(Gallery.id) as tagcounter FROM galleries Gallery
            JOIN gallery_tags tt ON Gallery.id = tt.gallery_id
            JOIN tags Tag ON tt.tag_id = Tag.id
            GROUP BY Tag.id
            ORDER BY tagcounter DESC
            LIMIT " . TAG_CLOUD_BIG);

           $this->Tag->mc_set($mc_key, $resultset, false, TAG_CLOUD_TTL);

        }
        $this->Tag->mc_close();

        $this->set('gallery_tags', $resultset);
    }
	
    function view($tag_name, $option = "views") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag) || $option ==='title')
		$this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$tag_id = $tag['Tag']['id'];
		//Listing deleted user 
		  $this->loadModel('User');  
 		  $deleted_users = $this->User->find('list', 
 		                array('conditions' =>  "User.status = 'delbyadmin' OR User.status = 'delbyusr'", 
 		                'fields' => 'id'));
		  $deleted_users_id = implode(',' ,$deleted_users);
		
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND ProjectTag.tag_id = $tag_id AND Project.user_id NOT IN (".$deleted_users_id.") GROUP BY project_id";

        if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
		}
		
		if ($option == "views") {
			$order = 'Project.views DESC';
		}
		
		if ($option == "loveits") {
			$order = 'Project.loveitsuniqueip DESC';
		}
		
		if ($option == "creation") {
			$order = 'Project.created DESC';
		}
		
		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$limit = NUM_TAGED_PROJECT;
		$mc_key = 'project_tag_'.$tag_id.'_'.$content_status.'_'. $option.'_'. $limit;
		$ttl = TAGED_PROJECT_CACHE_TTL; 
        $this->ProjectTag->mc_connect();
        $tag_projects = $this->ProjectTag->mc_get($mc_key);
        if($tag_projects === false) {
			$this->Project->unbindModel(array('hasMany' => array('GalleryProject')));
			$this->ProjectTag->unbindModel(array('belongsTo' => array('User')));
			$this->ProjectTag->recursive = 2;	
			$tag_projects = $this->ProjectTag->findAll($final_criteria, NULL , $order, NUM_TAGED_PROJECT);
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
	
	
	function view_galleries($tag_name, $option = "projects") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Galleries tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
            $this->cakeError('error404');
		$content_status = $this->getContentStatus();
		
		$this->Pagination->show = 10;
		$tag_id = $tag['Tag']['id'];
		$criteria = Array("tag_id" => $tag['Tag']['id']);
		$criteria = Array("ProjectTag.tag_id = $tag_id");
		$final_criteria = "Gallery.total_projects > 0 AND Gallery.visibility = 'visible' AND GalleryTag.tag_id = $tag_id GROUP BY gallery_id";
        $count_criteria = "Gallery.total_projects > 0 AND Gallery.visibility = 'visible' AND GalleryTag.tag_id = $tag_id";

		if ($content_status == "safe") {
			$final_criteria = "Gallery.status = 'safe' AND " . $final_criteria; 
			$count_criteria = "Gallery.status = 'safe' AND " . $count_criteria; 
		}
		
		$this->modelClass = "GalleryTag";

		if ($option == "projects") {
			$options = Array("sortBy"=>"total_projects", "sortByClass" => "Gallery", 
						"direction"=> "DESC", "url"=>"/tags/view_galleries/" . $tag_name . "/" . $option);
		}
		if ($option == "changed") {
			$options = Array("sortBy"=>"changed", "sortByClass" => "Gallery", 
						"direction"=> "DESC", "url"=>"/tags/view_galleries/" . $tag_name . "/" . $option);
		}
		if ($option == "title") {
			$options = Array("sortBy"=>"name", "sortByClass" => "Gallery", 
						"direction"=> "ASC", "url"=>"/tags/view_galleries/" . $tag_name . "/" . $option);
		}
		$final_count = $this->GalleryTag->findCount($count_criteria);
		list($order,$limit,$page) = $this->Pagination->init($final_criteria, Array(), $options, $final_count);
		$tag_galleries = $this->GalleryTag->findAll($final_criteria, null, $order, $limit, $page, 3);
			
		$tag_galleries = $this->set_galleries($tag_galleries);
		$tag_galleries = $this->finalize_galleries($tag_galleries);
		$this->set('option', $option);
        $this->set('tag_galleries', $tag_galleries);
		$this->set('tag_name', $tag_name);
        $this->set('tag', $tag);
        $this->render('galleries');
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
	
	function set_galleries($galleries) {
		$isLogged = $this->isLoggedIn();
		$user_id = $this->getLoggedInUserID();
		$return_galleries = Array();
		
		foreach ($galleries as $gallery) {
			$temp_gallery = $gallery;
			$current_user_id = $temp_gallery['Gallery']['user_id'];
			$temp_gallery['Gallery']['ignored'] = false;
			if ($isLogged) {
				$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id AND IgnoredUser.user_id = $current_user_id");
				if ($ignore_count > 0) {
					$temp_gallery['Gallery']['ignored'] = true;
				} else {
					$temp_gallery['Gallery']['ignored'] = false;
				}
			}
			array_push($return_galleries, $temp_gallery);
		}
		return $return_galleries;
	}
	
	/**
	* Expands the description of a project in the explorer view
	**/
	function expandDescription($project_id, $secondary = null) {
		$this->autoRender = false;
		$this->Project->id=$project_id;
        $project = $this->Project->read();
		$user_id = $this->getLoggedInUserID();	
		$isLogged = $this->isLoggedIn();
		
		$this->set('project', $project);
		$this->render('expand_description_ajax', 'ajax');
	}
	
	/**
	* Expands the description of a project in the explorer view
	**/
	function expandGalleryDescription($gallery_id, $secondary = null) {
		$this->autoRender = false;
		$this->Gallery->id=$gallery_id;
        $gallery = $this->Gallery->read();
		$user_id = $this->getLoggedInUserID();	
		$isLogged = $this->isLoggedIn();
		
		$gallery = $this->finalize_gallery($gallery);
		$this->set('gallery', $gallery);
		$this->render('expand_gallerydescription_ajax', 'ajax');
	}
}
