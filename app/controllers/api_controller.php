<?php

class ApiController extends AppController {

    var $uses = array('Project','User');

    /**
     * Redirects to project page based on a project id
     * @parm int $pid => project id
     */
    function getproject($pid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
        $this->redirect("/projects/" . $project['User']['urlname'] . "/" . $project['Project']['id']);                      
    }

    /**
     * Prints the path of a project based on the id
     * @parm int $pid => project id
     */
    function getprojectpath($pid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
        die("/projects/" . $project['User']['urlname'] . "/" . $project['Project']['id'] . ".sb");                      
	exit(1);
    }

    /** 
     * Redirects to user page based on a user id
     * @parm int $uid => project id
     */
    function getuser($uid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->User->id=$uid;
        $user = $this->User->read();
        $this->redirect("/users/" . $user['User']['username'] . "/");     
    } 
	
	/** 
     * Returns all registered users
     */
	function getregisteredusers(){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$registeredusers = $this->Project->mc_get("get-registered-users");
        if ($registeredusers === false) {
       	    $registeredusers = $this->__getTotalUsers();
            $this->Project->mc_set("get-registered-users", $registeredusers, false, API_REGISTERED_USERS_TTL);
        }
		echo $registeredusers;
		$this->Project->mc_close();
		exit;
	}
	/** 
     * Helper function to get all registered users
     */
	function __getTotalUsers() {
        $resultset =  $this->User->findCount("villager=0");
	return number_format(0.0 + $resultset);
    }
	
	/** 
     * Returns the number of people who have crated a project.
     */
	function getcreators(){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$tot_creators = $this->Project->mc_get("get-project-creators");
		if ($tot_creators === false) {
			$resultset =  $this->Project->query("SELECT count( DISTINCT user_id ) AS project_creators FROM projects"); 
			$tot_creators = $resultset['0']['0']['project_creators'];
			$tot_creators = number_format(0.0 + $tot_creators);
			$this->Project->mc_set("get-project-creators", $tot_creators, false, API_PROJECT_CREATORS_TTL);
		}
		echo $tot_creators;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns the total number of projects uploaded to the website
     */
	function gettotalprojects(){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$tot_projects = $this->Project->mc_get("get-total-projects");
		if ($tot_projects === false) {
			$resultset =  $this->Project->query("SELECT count( * ) AS tot_projects FROM projects"); 
			$tot_projects = $resultset['0']['0']['tot_projects'];
			$tot_projects = number_format(0.0 + $tot_projects);
			$this->Project->mc_set("get-total-projects", $tot_projects, false, API_TOTAL_PROJECT_TTL);
		}
		echo $tot_projects;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns sum of totalScripts 
     */
	function gettotalscripts(){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$tot_scripts = $this->Project->mc_get("get-total-scripts");
		if ($tot_scripts === false) {
			$resultset =  $this->Project->query("SELECT SUM(totalScripts) as totalscripts FROM projects"); 
			$tot_scripts = $resultset['0']['0']['totalscripts'];
			$tot_scripts = number_format(0.0 + $tot_scripts);
			$this->Project->mc_set("get-total-scripts", $tot_scripts, false, API_TOTAL_SCRIPTS_TTL);
		}
		echo $tot_scripts;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns sum of numberOfSprites 
     */
	function gettotalsprites(){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$tot_sprites = $this->Project->mc_get("get-total-sprites");
		if ($tot_sprites === false) {
			$resultset =  $this->Project->query("SELECT SUM(numberOfSprites) as totalsprites FROM projects"); 
			$tot_sprites = $resultset['0']['0']['totalsprites'];
			$tot_sprites = number_format(0.0 + $tot_sprites);
			$this->Project->mc_set("get-total-sprites", $tot_sprites, false, API_TOTAL_SPRITES_TTL);
		}
		echo $tot_sprites;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns all visible project of user
	 @param username 
     */
	function getprojectsbyusername($username){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$user_id = $this->User->field('id', array('User.username' => $username));
		$mc_key = 'get-user-projects-'.$user_id;
		$project_ids = $this->Project->mc_get($mc_key);
		if ($project_ids === false) {
			$user_id = $this->User->field('id', array('User.username' => $username));
			$projects = $this->Project->find('all', array('conditions'=>array('Project.user_id' => $user_id, 'Project.proj_visibility'=>'visible'), 'fields'=> 'id', 'recursive'=> -1, 'order' =>'created DESC'));
			$project_list =  Set::extract('/Project/id', $projects);
			$project_ids = implode(':', $project_list);
			$this->Project->mc_set($mc_key, $project_ids, false, API_USER_PROJECTS_TTL);
		}
		
		echo $project_ids;
		$this->Project->mc_close();
		exit;
	}
	
	
	/** 
     * Returns all friends of user
	 @param username 
     */
	function getfriendsbyusername($username){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$user_id = $this->User->field('id', array('User.username' => $username));
		$mc_key = 'get-user-friends-'.$user_id;
		$friend_ids = $this->Project->mc_get($mc_key);
		if ($friend_ids === false) {
			
			$friends = $this->Relationship->find('all', array('conditions'=>array('Relationship.user_id' => $user_id, 'Relationship.friend_id > 0'), 'fields'=> 'friend_id', 'recursive'=> -1, 'order' =>'Relationship.timestamp DESC'));
			$friends_list =  Set::extract('/Relationship/friend_id', $friends);
			$friend_ids = implode(':', $friends_list);
			$this->Project->mc_set($mc_key, $friend_ids, false, API_USER_FRIENDS_TTL);
		}
		
		echo $friend_ids;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns all galleries of user
	 @param username 
     */
	function getgalleriesbyusername($username){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$user_id = $this->User->field('id', array('User.username' => $username));
		$mc_key = 'get-user-galleries-'.$user_id;
		$gallery_ids = $this->Project->mc_get($mc_key);
		if ($gallery_ids === false) {
			
			$galleries = $this->Gallery->find('all', array('conditions'=>array('Gallery.user_id' => $user_id, 'Gallery.visibility' => 'visible'), 'fields'=> 'id', 'recursive'=> -1, 'order' =>'Gallery.created DESC'));pr($galleries);
			$gallery_list =  Set::extract('/Gallery/id', $galleries);
			$gallery_ids = implode(':', $gallery_list);
			$this->Project->mc_set($mc_key, $gallery_ids, false, API_USER_GALLERIES_TTL);
		}
		
		echo $gallery_ids;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns  user's info
	 @param username 
     */
	function getinfobyusername($username){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$user_id = $this->User->field('id', array('User.username' => $username));
		$mc_key = 'get-user-info-'.$user_id;
		$user_info = $this->Project->mc_get($mc_key);
		if ($user_info === false) {
			$user_details = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'fields'=> array('id', 'username', 'country')));
			$user_info = $user_details['User']['username'].':'.$user_details['User']['id'].':'.$user_details['User']['country'];
			$this->Project->mc_set($mc_key, $user_info, false, API_USER_INFO_TTL);
		}
		
		echo $user_info;
		$this->Project->mc_close();
		exit;
	}
	
	
	/** 
     * Returns  all projects of a gallery
	 @param galleryid 
     */
	function getprojectsbygallery($gallery_id){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$mc_key = 'get-projects-by-gallery-'.$gallery_id;
		$projects = $this->Project->mc_get($mc_key);
		if ($projects === false) {
			$gallery_projects = $this->GalleryProject->find('all', array('conditions'=>array('GalleryProject.gallery_id' => $gallery_id, 'Gallery.visibility' => 'visible'), 'recursive'=>2,'order' =>'GalleryProject.timestamp DESC'));
			foreach($gallery_projects as $project){
				$project_list[] = $project['Project']['User']['username'].':'.$project['Project']['id'];
			}
			
			$projects = implode(',', $project_list);
			$projects = str_replace(',','<BR>',$projects);
			$this->Project->mc_set($mc_key, $projects, false, API_PROJECTS_BY_GALLERY_TTL);
		}
		
		echo $projects;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns project info
	 @param project_id 
     */
	function getprojectinfobyid($project_id){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$mc_key = 'get-project-info-'.$project_id;
		$project_info = $this->Project->mc_get($mc_key);
		if ($project_info  === false) {
	
			$this->Project->bindHABTMTag();
			$projects = $this->Project->find('first', array('conditions'=>array('Project.id' => $project_id), 'fields'=> array('id', 'name','description', 'created', 'country'), 'recursive'=> 1));
			$tag_list =  Set::extract('/Tag/name', $projects);
			$projects['Project']['tags'] = implode(',', $tag_list);
			$project_info = $projects['Project']['name'].':'.$projects['Project']['description'].':'.$projects['Project']['created'].':'.$projects['Project']['tags'].':'.$projects['Project']['country'];
			$this->Project->mc_set($mc_key, $project_info , false, API_PROJECT_INFO_TTL);
		}
		echo $project_info;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns user authentication
	 @param username,password 
     */
	function authenticateuser($username, $password){
		Configure::write('debug', 0);
		$user_record = $this->User->find('first', array('conditions' => array('User.username'=>$username, 'User.password'=>sha1($password)),'fields'=>array('id', 'username')));
		if(empty($user_record)){
			$user_info = 'false';
		}else{
			$user_info = $user_record['User']['id'].':'.$user_record['User']['username'];
		}
		echo $user_info;
		exit;
	}
}
?>
