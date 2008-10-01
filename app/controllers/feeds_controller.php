<?php
class FeedsController extends AppController {
    var $name = 'Feeds';
    var $components = array('Session', 'Cookie');
	var $uses = array('Flagger', 'User', 'FeaturedProject', 'Project', 'Gallery', 'GalleryProject');
	
	/**
	* Test
	**/
	function test() {
		$this->layout = 'xml'; 
        $this->set('projects', $this->Project->findAll(null, null, null, 1));
	}
	
	function getNewestProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		
		$projects = $this->Project->findAll("", null, "Project.created DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getNewestProjects";
		
		foreach ($projects as $current_project) {
			$project_id = $current_project['Project']['id'];
			$user_id = $current_project['Project']['user_id'];
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			array_push($final_projects, $current_project);
		}

		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('newest_project_feed');
	}
	
	function getFeaturedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		
		$projects = $this->FeaturedProject->findAll("", null, "FeaturedProject.timestamp DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getFeaturedProjects";
		
		foreach ($projects as $current_project) {
			$project_id = $current_project['Project']['id'];
			$user_id = $current_project['Project']['user_id'];
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			array_push($final_projects, $current_project);
		}

		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('featured_project_feed');
	}
	
	function getTopLovedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		
		$projects = $this->Project->findAll("Project.loveit > 0", null, "Project.loveit DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getUnreviewedProjects";
		
		foreach ($projects as $current_project) {
			$project_id = $current_project['Project']['id'];
			$user_id = $current_project['Project']['user_id'];
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			array_push($final_projects, $current_project);
		}

		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('toploved_project_feed');
	}
	
	function getTopFlaggedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		
		$projects = $this->Flagger->findAll("", null, "Flagger.timestamp DESC", 100);
		$final_projects = Array();
		$flagged_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getTopFlaggedProjects";
		
		$counter = 0;
		foreach ($projects as $flagger) {
			$project_id = $flagger['Flagger']['project_id'];
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			
			$current_project = $this->Project->find("Project.id = $project_id");
			$user_id = $current_project['Project']['user_id'];
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];

			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			
			if (in_array($project_id, $flagged_projects)) {
			} else {
				array_push($final_projects, $current_project);
				array_push($flagged_projects, $project_id);
			}
			$counter++;
			if ($counter >= 16) {
				break;
			}
		}

		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('topflagged_project_feed');
	}
	
	function getTopRemixedProjects() {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		
		$projects = $this->Project->findAll("Project.remixes > 0", null, "Project.remixes DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getTopRemixedProjects";
		
		foreach ($projects as $current_project) {
			$project_id = $current_project['Project']['id'];
			$user_id = $current_project['Project']['user_id'];
			$user = $this->User->find("User.id = $user_id");
			$user_name = $user['User']['username'];
			
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			array_push($final_projects, $current_project);
		}

		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('topremixed_project_feed');
	}
	
	/**
	* RSS Feed for Recent Gallery Projects
	**/
	function getRecentUserProjects($user_id) {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		$this->User->id = $user_id;
		$user = $this->User->read();
		$user_name = $user['User']['username'];
		
		$projects = $this->Project->findAll("user_id = $user_id AND proj_visibility = 'visible'", null, "Project.created DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getRecentUserProjects/$user_id";
		
		foreach ($projects as $current_project) {
			$project_id = $current_project['Project']['id'];
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;

			$description = $current_project['Project']['description'];
			$description = $this->removespecialchars($description);
			$current_project['Project']['description'] = $description;
			array_push($final_projects, $current_project);
		}
		$this->set('user_name', $user_name);
		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('User_project_feed');
	}
	
	/**
	* RSS Feed for Recent Gallery Projects
	**/
	function getRecentGalleryProjects($gallery_id) {
		$this->autoRender = false;
		$this->layout = 'xml'; 
		$this->Gallery->id = $gallery_id;
		$gallery = $this->Gallery->read();
		$gallery_name = $gallery['Gallery']['name'];
		
		$gallery_projects = $this->GalleryProject->findAll("gallery_id = $gallery_id AND Project.proj_visibility = 'visible'", null, "GalleryProject.timestamp DESC", 15);
		$final_projects = Array();
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$rss_link = "http://" . $url . "/feeds/getRecentGalleryProjects/$gallery_id";
		
		foreach ($gallery_projects as $current_project) {
			$user_id = $current_project['Project']['user_id'];
			$user_record = $this->User->findAll("User.id = $user_id");
			$user_name = $user_record[0]['User']['username'];
			$project_id = $current_project['Project']['id'];
			
			$thumbnail_src = getThumbnailImg($user_name, $project_id);
			$additional_url = "/projects/$user_name/$project_id";
			$current_project['Project']['link'] = "http://" . $url . $additional_url;
			$current_project['Project']['image_link'] = "http://" . $url . $thumbnail_src;
			$current_project['Project']['description'] = $current_project['Project']['description'];
			array_push($final_projects, $current_project);
		}
		
		$this->set('gallery_name', $gallery_name);
		$this->set('rss_link', $rss_link);
		$this->set('projects', $final_projects);
		$this->render('gallery_project_feed');
	}
	
	function removespecialchars($target) {
		$result = $target;
		$search = "«";
		$replace = "-";
		$result = str_replace($search, $replace, $target);
		return $result;
	}
}
?>
