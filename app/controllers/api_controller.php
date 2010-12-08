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
     * Redirects to user page based on a user id
     * @parm int $uid => project id
     */
    function getusernamebyid($uid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->User->id=$uid;
        $user = $this->User->read();
        echo $user['User']['username'];
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
			
			$galleries = $this->Gallery->find('all', array('conditions'=>array('Gallery.user_id' => $user_id, 'Gallery.visibility' => 'visible'), 'fields'=> 'id', 'recursive'=> -1, 'order' =>'Gallery.created DESC'));
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
			$username = rawurlencode($user_details['User']['username']);
			$user_info = $username.':'.$user_details['User']['id'].':'.$user_details['User']['country'];
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
				$username = rawurlencode($project['Project']['User']['username']);
				$project_list[] = $username.':'.$project['Project']['id'];
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
	 results->user_id:name:description:created:tags:country:loveit:num_favoriters:remixer:remixes:numberOfSprites:totalScripts:numberofcomments:numberofdownloads 
     */
	function getprojectinfobyid(){
			Configure::write('debug', 0);
			 $arg_list = array();
			 foreach($this->params['pass'] as $param){
				$arg_list[] = $param;
			 }
			 $args = implode(',' , $arg_list);
			 $mc_args = implode('_' , $arg_list);
		
			$this->Project->mc_connect();
			$mc_key = 'get-project-info-'.$mc_args;
			$project_info = $this->Project->mc_get($mc_key);
			if ($project_info  === false) {
	
			$this->Project->bindHABTMTag();
			$this->Project->bindPcomment(array('Pcomment.comment_visibility'=>'visible'));
			$projects = $this->Project->find('all', array('conditions'=>"Project.id IN(".$args.")", 'fields'=> array('id','user_id','name','description', 'created', 'country', 'loveit', 'num_favoriters', 'remixer', 'remixes', 'numberOfSprites', 'totalScripts'), 'recursive'=> 1));
			 $project_list =array();
			 App::import("Model","Downloader");
			 $this->Downloader = & new Downloader();
			 foreach($projects as $project){
				$downloader_record = $this->Downloader->find('count', array('conditions'=>array('project_id'=>$project['Project']['id'])));		
				$tag_list =  Set::extract('/Tag/name', $project);
				$project['Project']['tags'] = implode(',', $tag_list);
				
				$project_name = rawurlencode($project['Project']['name']);
				$project_desc = rawurlencode($project['Project']['description']);
				$project_tags = rawurlencode($project['Project']['tags']);
				$project_created = rawurlencode($project['Project']['created']);
			 	$project_list[] = $project['Project']['user_id'].':'.$project_name.':'.$project_desc.':'.$project_created.':'.$project_tags.':'.$project['Project']['country'].':'.$project['Project']['loveit'].':'.$project['Project']['num_favoriters'].':'.$project['Project']['remixer'].':'.$project['Project']['remixes'].':'.$project['Project']['numberOfSprites'].':'.$project['Project']['totalScripts'].':'.count($project['Pcomment']).':'.$downloader_record;
			 }
			   $project_info = implode('##', $project_list);
			   $project_info = str_replace('##','<BR>',$project_info);
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
	function authenticateuser(){
		Configure::write('debug', 0);
		$username  = $_GET['username'];
		$password  = rawurldecode($_GET['password']);
		$this->User->bindModel( array('hasOne' => array('BlockedUser')) );
		$user_record = $this->User->find('first', array('conditions' => array('User.username'=>$username, 'User.password'=>sha1($password)),'fields'=>array('BlockedUser.*','User.id', 'User.username')));
		if(empty($user_record)){
			$user_info = 'false';
		}else{
			
			$username = rawurlencode($user_record['User']['username']);
			if(isset($user_record['BlockedUser']['user_id']) && !empty($user_record['BlockedUser']['user_id']))
				$status = 'blocked';
			else
				$status = 'unblocked';
			
			$user_info = $user_record['User']['id'].':'.$username.':'.$status;
		}
		echo $user_info;
		exit;
	}
	
	
	/** 
     * Returns gallery info
	 @param gallery_id  one or more as parameter
	 results:author:name:description:total project:usgae:staus,visibility:created
     */
	function getgalleryinfobyid(){
		Configure::write('debug', 0);
		$arg_list = array();
		foreach($this->params['pass'] as $param){
			$arg_list[] = $param;
		}
		$args = implode(',' , $arg_list);
		$mc_args = implode('_' , $arg_list);
		$this->Gallery->mc_connect();
		$mc_key = 'get-gallery-info-'.$mc_args;
		$gallery_info = $this->Gallery->mc_get($mc_key);
		if ($gallery_info  === false) {
			$galleries = $this->Gallery->find('all', array('conditions'=>"Gallery.id IN(".$args.")", 'fields'=> array('id','user_id','name','description', 'created', 'total_projects','usage', 'status', 'visibility'), 'recursive'=> 1));
			$gallery_list =array();
			foreach($galleries as $gallery){
				$gallery_name = rawurlencode($gallery['Gallery']['name']);
				$gallery_desc = rawurlencode($gallery['Gallery']['description']);
				$gallery_created = rawurlencode($gallery['Gallery']['created']);
			    $gallery_list[] = $gallery['Gallery']['user_id'].':'.$gallery_name.':'.$gallery_desc.':'.$gallery['Gallery']['total_projects'].':'.$gallery['Gallery']['usage'].':'.$gallery['Gallery']['status'].':'.$gallery['Gallery']['visibility'].':'.$gallery_created;
			}
			$gallery_info = implode('#', $gallery_list);
			$gallery_info = str_replace('#','<BR>',$gallery_info);
			$this->Gallery->mc_set($mc_key, $gallery_info , false, API_GALLERY_INFO_TTL);
		}
		echo $gallery_info;
		$this->Gallery->mc_close();
		exit;
	}
	
	/** 
     * Returns number of project visible/all depends on parameter onlyvisible(yes/no)
	 By default only visible projects
     */
	function getnumprojectsbyuser($username){
		Configure::write('debug', 0);
		$user_id = $this->User->field('id', array('User.username' => $username));
		$final_criteria = "Project.user_id = $user_id AND Project.proj_visibility = 'visible' AND Project.status != 'notsafe'";
		
		if(isset($_GET['onlyvisible']) && $_GET['onlyvisible'] == 'no'){
			$final_criteria = "Project.user_id = $user_id";
		} 
		$this->Project->recursive =-1;
		echo $projects = $this->Project->find('count',array('conditions'=>$final_criteria));
		exit;
	}
	
	
	/** 
     * Returns all visible pcomment
	 @param pid
	 results:user_id:createddate:comment
     */
	function getpcommentsbyid ($pid){
		Configure::write('debug', 0);
		
		$this->Project->mc_connect();
		$mc_key = 'get-pcomments-'.$pid;
		$pcomment_info = $this->Project->mc_get($mc_key);
		
		if ($pcomment_info  === false) {
			$this->Pcomment->unbindModel(array('belongsTo' => array('User', 'Project')));
		$pcomments = $this->Pcomment->find('all', array('conditions'=> array('Pcomment.project_id'=>$pid, 'Pcomment.comment_visibility'=>'visible')));
			$pcomments_list =array();
			foreach($pcomments as $pcomment){
				$user_id = $pcomment['Pcomment']['user_id'];
				$comment_id = $pcomment['Pcomment']['id'];
				$reply_to_comment_id = $pcomment['Pcomment']['reply_to'];
				if($reply_to_comment_id == -100){
					$reply_to_comment_id = "";
				}
				$content = rawurlencode($pcomment['Pcomment']['content']);
				$timestamp = rawurlencode($pcomment['Pcomment']['timestamp']);
			    $pcomments_list[] = $user_id.':'.$comment_id.':'.$reply_to_comment_id.':'.$timestamp.':'.$content;
			}
			$pcomment_info = implode('##', $pcomments_list);
			$pcomment_info = str_replace('##','<BR>',$pcomment_info);
			$this->Project->mc_set($mc_key, $pcomment_info, false, API_PCOMMENT_BY_ID_TTL);
		}
		echo $pcomment_info ;
		$this->Project->mc_close();
		exit;
	}
	
	/** 
     * Returns  all favoriteproject
	 @param userid 
     */
	function getusersfavoriteprojectsbyuid($uid=null){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$mc_key = 'get-favorite-projects-by-uid-'.$uid;
		$projects = $this->Project->mc_get($mc_key);
		if ($projects === false) {
			App::import("Model","Favorite");
			$this->Favorite		= & new Favorite();
			$favorite_projects = $this->Favorite->find('all', array('conditions'=>array('Favorite.user_id' => $uid), 'fields'=>array('Favorite.*'),'recursive'=>1,'order' =>'Favorite.timestamp DESC'));
			$favorites_ids = Set::extract('/Favorite/project_id', $favorite_projects);
			$projects = implode(':', $favorites_ids);
			
			$this->Project->mc_set($mc_key, $projects, false, API_FAVORITE_PROJECTS_BY_UID_TTL);
		}
		
		 echo $projects;
		 $this->Project->mc_close();
		exit;
	}
	
	/*
	*return the block count data of latest project version
	*params project_id
	*/
	function getprojectblockscount($pid=null){
		if(empty($pid)){
			$errorMsg = array('error' =>'Invalid project id');
			echo json_encode($errorMsg);
			exit;
		}
		Configure::write('debug', 0);
		$this->loadModel('ProjectBlock');
		$this->ProjectBlock->mc_connect();
		$mc_key = 'get-project-block-count-'.$pid;
		$data = $this->ProjectBlock->mc_get($mc_key);
		
		if ($data === false) {
			$data = $this->ProjectBlock->find('first',
												array('conditions'=>array('ProjectBlock.project_id'=> $pid),
												'order' => 'ProjectBlock.project_version DESC')
										);
			$this->ProjectBlock->mc_set($mc_key, $data, false, API_PROJECT_BLOCK_COUNT_TTL);
		}								
		if(empty($data)){
			$errorMsg = array('error' =>'Invalid project id');
			echo json_encode($errorMsg);
			exit;
		}
		echo json_encode($data['ProjectBlock']);
		exit;
	}//eof
	
	/*
	*return the human_readable data of latest project version
	*params project_id
	*/
	function getprojectblocks($pid=null){
		if(empty($pid)){
			$errorMsg = array('error' =>'Invalid project id');
			echo json_encode($errorMsg);
			exit;
		}
		Configure::write('debug', 0);
		$pid = intval($pid);
		$this->loadModel('ProjectSpriteBlocksStack');
		$this->ProjectSpriteBlocksStack->mc_connect();
		$mc_key = 'get-project-block-'.$pid;
		$data = $this->ProjectSpriteBlocksStack->mc_get($mc_key);
		if ($data === false) {
			$sql = "SELECT * FROM `project_sprite_blocks_stack`
					WHERE `project_id` =$pid 
					AND `project_version`=(SELECT MAX(`project_version`) FROM `project_sprite_blocks_stack`
											WHERE `project_id`=$pid)
					ORDER BY sprite_id ASC";
			uses('Sanitize');
			Sanitize::clean($sql, array('encode' => false));
			$results = $this->ProjectSpriteBlocksStack->query($sql);
			if(empty($results)){
				$errorMsg = array('error' =>'Invalid project id');
				echo json_encode($errorMsg);
				exit;
			}
			$data =array();
			$temp = array();
			$data['project_id'] = $results['0']['project_sprite_blocks_stack']['project_id'];
			foreach($results as $result){
				$temp[] = $result['project_sprite_blocks_stack']['human_readable'];
			}
			$data['sprites'] = $temp;
			
			$this->ProjectSpriteBlocksStack->mc_set($mc_key, $data, false, API_PROJECT_BLOCK_TTL);
		}								
		echo json_encode($data);
		exit;
	}//eof
}//class
?>
