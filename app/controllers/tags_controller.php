<?php
Class TagsController extends AppController {

    var $uses = array('IgnoredUser', 'Project','ProjectTag', 'Tag', 'Gallery', 'GalleryTag', 'Notification');
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
        $resultset = $this->Tag->query("
            SELECT Tag.name, COUNT(Project.id) as tagcounter FROM projects Project
            JOIN project_tags tt ON Project.id = tt.project_id
            JOIN tags Tag ON tt.tag_id = Tag.id
            GROUP BY Tag.id
            ORDER BY tagcounter DESC
            LIMIT " . TAG_CLOUD_BIG);
        $this->set('tags', $resultset);
    }
	
	function __setGalleryTagCloud() {
        $resultset = $this->Tag->query("
            SELECT Tag.name, COUNT(Gallery.id) as tagcounter FROM galleries Gallery
            JOIN gallery_tags tt ON Gallery.id = tt.gallery_id
            JOIN tags Tag ON tt.tag_id = Tag.id
            GROUP BY Tag.id
            ORDER BY tagcounter DESC
            LIMIT " . TAG_CLOUD_BIG);
        $this->set('gallery_tags', $resultset);
    }
	
    function view($tag_name, $option = "views") {
		$this->autoRender = true;
		$this->pageTitle = ___("Scratch | Projects tagged with", true) . " '" . htmlspecialchars($tag_name) . "'";
        $tag =  $this->Tag->find("name = '$tag_name'");
        if (empty($tag))
            $this->__err();
		$content_status = $this->getContentStatus();
		
		$this->Pagination->show = 10;
		$tag_id = $tag['Tag']['id'];
		$criteria = Array("tag_id" => $tag['Tag']['id']);
		$criteria = Array("ProjectTag.tag_id = $tag_id");
		$final_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND ProjectTag.tag_id = $tag_id GROUP BY project_id";
        $count_criteria = "(Project.proj_visibility = 'visible' OR Project.proj_visibility = 'censbycomm' OR Project.proj_visibility = 'censbyadmin') AND ProjectTag.tag_id = $tag_id";
		
		$distinct_criteria = "DISTINCT project_id, tag_id, user_id"; 
		if ($content_status == "safe") {
			$final_criteria = "Project.status = 'safe' AND " . $final_criteria; 
			$count_criteria = "Project.status = 'safe' AND " . $count_criteria; 
		}
		
		$this->Project->bindUser();
        $this->ProjectTag->bindProject(); 
        $this->ProjectTag->unbindTag();
		
		$this->modelClass = "ProjectTag";

		if ($option == "views") {
			$options = Array("sortBy"=>"views", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/tags/view/" . $tag_name . "/" . $option);
		}
		if ($option == "title") {
			$options = Array("sortBy"=>"name", "sortByClass" => "Project", 
						"direction"=> "ASC", "url"=>"/tags/view/" . $tag_name . "/" . $option);
		}
		if ($option == "loveits") {
			$options = Array("sortBy"=>"loveit", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/tags/view/" . $tag_name . "/" . $option);
		}
		if ($option == "creation") {
			$options = Array("sortBy"=>"created", "sortByClass" => "Project", 
						"direction"=> "DESC", "url"=>"/tags/view/" . $tag_name . "/" . $option);
		}
		$final_count = $this->ProjectTag->findCount($count_criteria);
		list($order,$limit,$page) = $this->Pagination->init($this->Project->addVisCheck($final_criteria), Array(), $options, $final_count);
		$tag_projects = $this->ProjectTag->findAll($final_criteria, null, $order, $limit, $page, 3);
			
		$tag_projects = $this->set_projects($tag_projects);
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
            $this->__err();
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
