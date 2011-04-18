<?php
/*******************************************************************************/
##Note:Result is encoded with php function rawurlencode().
/*******************************************************************************/
class ApiController extends AppController {

    var $uses = array('Project','User');

   /*
	* All functions allow cross-site execution
   */
    function beforeFilter() {
       header("Access-Control-Allow-Origin: *");
    }

    /**
	* Name:getproject
	* This function takes project id as input and redirects to project page based on a project id
	* Parameter: project id
	* Example: http://scratch.mit.edu/api/getproject/1211743
	* Output: it will redirect to http://scratch.mit.edu/projects/Dab1998/1211743
    */
    function getproject($pid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->Project->id=$pid;
        $project = $this->Project->read();
        $this->redirect("/projects/" . $project['User']['urlname'] . "/" . $project['Project']['id']);                      
    }

    /**
	* Name: getprojectpath
	* This function takes project id as input and print the .sb file path of a project
	* Parameter: project id
	* Example: http://scratch.mit.edu/api/getprojectpath/1211743
	* Output: /projects/Dab1998/1211743.sb
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
    * This function takes user id as input and redirects to user's my stuff page based on a user id.
    * Parameter: user id.
	* Example: http://scratch.mit.edu/api/getuser/139
	* Output: it will redirect to http://scratch.mit.edu/users/andresmh/
    */
    function getuser($uid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->User->id=$uid;
        $user = $this->User->read();
        $this->redirect("/users/" . $user['User']['username'] . "/");     
    } 

    /** 
	* This function takes user id as input and print username.
	* Parameter: user id.
	* Example: http://scratch.mit.edu/api/getusernamebyid/139
	* Output: andresmh
	*/
    function getusernamebyid($uid=null) {
        $this->autoRender=false;
        $this->Project->bindUser();
        $this->User->id=$uid;
        $user = $this->User->read();
        echo $user['User']['username'];
    }

	
	/** 
    * This function returns total number of registered users.
	* Parameter: none.
	* Example: http://scratch.mit.edu/api/getregisteredusers
	* Output: 682,139
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
    * This function returns the number of people who have created a project.
	* Parameter: none.
	* Example: http://scratch.mit.edu/api/getcreators
	* Output: 194,830
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
    * This function returns the total number of projects uploaded by users.
	* Parameter: none.
	* Example: http://scratch.mit.edu/api/gettotalprojects
	* Output: 1,484,878
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
    * This function returns sum of total Scripts.
	* Parameter: none.
	* Example: http://scratch.mit.edu/api/gettotalscripts
	* Output: 25,806,007
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
    * This function returns sum of number of sprites.
	* Parameter: none.
	* Example: http://scratch.mit.edu/api/gettotalsprites.
	* Output: 8,746,913
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
    * This function returns all id of visible project separated with colon (:) created by an user.
	* Parameter: username
	* Example: http://scratch.mit.edu/api/getprojectsbyusername/ashok
	* Output: 20805:920784:920767:920759:785614:715431:609769:608275:490870:490868:397124:371906
    */
	function getprojectsbyusername($username){
		Configure::write('debug', 0);
		$this->Project->mc_connect();
		$user_id = $this->User->field('id', array('User.username' => $username));
		if(empty($user_id)) {
			echo "";
			exit;
		}
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
    * This function returns all friend id of an user separated with colon (:).
	* Parameter: username
	* Example: http://scratch.mit.edu/api/getfriendsbyusername/ashok
	* Output: 273837:261892:140467:9811:183315:82753:184712:185632:183245:184333
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
    * This function returns all gallery id created by an user separated with colon (:).
	* Parameter: username
	* Example: http://scratch.mit.edu/api/getgalleriesbyusername/ashok
	* Output: 27321:24994
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
    * This function returns profile information of an user.
	* Parameter: username
	* Output: username: id: country
	* Example: http://scratch.mit.edu/api/getinfobyusername/ashok
	* Example output: ashok:139123:India
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
    * This function returns all projects (creator  and project id) of a gallery. Result contains one project details per line.
	* Parameter: gallery id
	* Output: project creator:project id
	* Example: http://scratch.mit.edu/api/getprojectsbygallery/27321
	* Example output: UnitedChuckVids:255443
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
    * This function returns project information. Result contains one project details per line.
	* Parameter: project id(one or more project ids)
	* Output: author_id:name:description:created:tags:country:loveit:num_favoriters:remixer:remixes:numberOfSprites
	:totalScripts:numberofcomments:numberofdownloads
	* Example: http://scratch.mit.edu/api/getprojectinfobyid/1210197
	* Example output:
261892:Snake:Eat%20the%20fruits%2C%20and%20don%27t%20touch%20the%20walls%20or%20your%20own%20tail.%20%20Use%20the%20arrow%20keys%20to%20navigate.Get%20the%20Extra%20points%20as%20soon%20as%20u%20can.:2010-07-24%2007%3A32%3A08:game%2Cfruit%2Csnake:BD:7:3:14:15:4:9:31:24
	Example:  http://scratch.mit.edu/api/getprojectinfobyid/785614/490868
	Example output: 139123:3%20FishChomp%20remix:Try%20to%20make%20the%20big%20fish%20eat%20the%20smaller%20fish.%0D%0DINSTRUCTIONS%0DClick%20the%20Green%20Flag%20to%20start.%20Move%20the%20mouse%20to%20control%20the%20big%20fish.%0D%0DHOW%20I%20MADE%20THIS%0D%2A%20To%20see%20whether%20the%20big%20fish%20is%20close%20enough%20to%20eat%20the%20little%20fish%2C%20I%20used%20the%20%22color%20_%20is%20over%20_%3F%22%20block%0D%2A%20The%20little%20fish%20broadcasts%20%22got%20me%22%20when%20the%20big%20fish%20gets%20close%20to%20it%2C%20which%20triggers%20the%20big%20fish%20to%20animate%20its%20mouth%20in%20an%20eating%20motion.%0D%0DMORE%20IDEAS%0D%2A%20Keep%20score%20of%20how%20many%20fish%20are%20eaten%0D%2A%20Make%20different%20kinds%20of%20fish%20%28%22good%20fish%22%20and%20%22bad%20fish%22%29:2009-04-16%2002%3A31%3A45:::::1:1:5:5:2:0
	139123:FISHVILLE:this%20is%20just%20a%20prctice%20project:2009-12-02%2000%3A27%3A17:::::0:0:4::0:0
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
	* This function returns user information if success and  'false'. if authentication fails.
	* Parameter: username,password
	* Note: password should be encoded using rawurlencode() method.So if your password is "demo#123" you need to use password as
	http://scratch.mit.edu/api/authenticateuser?username=demo&password=demo%23123
	where "demo%23123" is rawurlencode format of "demo#123"
	* Output:user_id:username:status
	* Example:http://scratch.mit.edu/api/authenticateuser?username=demo&password=demo123 
	* Example output: 12345:demo: normal
	*/
	function authenticateuser(){
		Configure::write('debug', 0);
		$username  = $_GET['username'];
		$password  = rawurldecode($_GET['password']);
		$this->User->bindModel( array('hasOne' => array('BlockedUser')) );
		$user_record = $this->User->find('first', array('conditions' => array('User.username'=>$username, 'User.password'=>sha1($password)),'fields'=>array('BlockedUser.*','User.id', 'User.username')));
		if(empty($user_record)){
			sleep(rand(0,10));
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
    * This function returns gallery information. Result contains one gallery details per line.
	* Parameter: gallery id(one or more gallery ids)
	* Output: author_id:name:description:total project:usgae:staus,visibility:created

	* Example: http://scratch.mit.edu/api/getgalleryinfobyid/24994 
	* Example output: 139123:scratchr:http%3A%2F%2Fscratch.mit.edu%2Fgalleries%2Fview%2F24994:0:friends:notreviewed:visible:2008-07-25%2005%3A48%3A20 

	* Example:  http://scratch.mit.edu/api/getgalleryinfobyid/24994
	* Example output:  139123:scratchr:http%3A%2F%2Fscratch.mit.edu%2Fgalleries%2Fview%2F24994:0:friends:notreviewed:visible:2008-07-25%2005%3A48%3A20
	139123:ashoks%20gallery:this%20is%20my%20gallery:1:private:notreviewed:visible:2008-08-25%2017%3A54%3A08
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
	* This function returns number of project visible/all depends on parameter onlyvisible(yes/no) created by user. By default  result contains only visible projects.
	* Parameter: username
	* Output: number of projects.
	* Example:  http://scratch.mit.edu/api/getnumprojectsbyuser/ashok
	* Example output: 12

	* Example:  http://scratch.mit.edu/api/getnumprojectsbyuser/ashok?onlyvisible='no'
	* Example output: 12
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
    * This function returns all visible comments of a project. Result contains one comment details per line.
	* Parameter: project id
	* Output:author_id:comment_id:reply_to_comment_id,createddate:comment
	* Example: http://scratch.mit.edu/api/getpcommentsbyid/255443 
	* Example output: 
144037:897255::2008-09-02%2012%3A26%3A46:cool%20n%20gr8%2C%20only%20i%20dont%20get%20the%20end
155300:898467::2008-09-02%2019%3A02%3A48:he%20get%27s%20hurt%20then%20he%20blasts%20%27em%0D%0Aoh%20and%20sorry%20it%20was%2019%20not%2018%0D%0A%28school%20just%20started%20I%27m%20losing%20my%20mind%29
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
    * This function returns all favorite  project id of user separated with colon(:)
	* Parameter: user id
	* Output:project_id:project_id
	* Example: http://scratch.mit.edu/api/getusersfavoriteprojectsbyuid/261892 
	* Example output: 766560:703044
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
	
	/**
	* This function returns block count code of latest version of the project (in json format)
	* Parameter: project id
	* Example: http://scratch.mit.edu/api/getprojectblockscount/4447
	* Output: {"project_id":"4447","project_version":"2","scratchComment":"0","KeyEventHatMorph":"0","EventHatMorph_StartClicked":"3","EventHatMorph":"2","MouseClickEventHatMorph":"1","WhenHatBlockMorph":"0","and_operator":"0","multiply_operator":"0","add_operator":"0","subtract_operator":"0","divide_operator":"0","isLessThan":"0","isEqualTo":"0","isGreaterThan":"0","mod_operator":"0","or_operator":"0","abs":"0","allMotorsOff":"0","allMotorsOn":"0","answer":"0","append_toList_":"0","backgroundIndex":"0","bounceOffEdge":"0","broadcast_":"1","changeBackgroundIndexBy_":"0","changeBlurBy_":"0","changeBrightnessShiftBy_":"0","changeCostumeIndexBy_":"0","changeFisheyeBy_":"0","changeGraphicEffect_by_":"2","changeHueShiftBy_":"0","changeMosaicCountBy_":"0","changePenHueBy_":"0","changePenShadeBy_":"0","changePenSizeBy_":"0","changePixelateCountBy_":"0","changePointillizeSizeBy_":"0","changeSaturationShiftBy_":"0","changeSizeBy_":"0","changeStretchBy_":"0","changeTempoBy_":"0","changeVar_by_":"0","changeVisibilityBy_":"0","changeVolumeBy_":"0","changeWaterRippleBy_":"0","changeWhirlBy_":"0","changeXposBy_":"0","changeYposBy_":"2","clearPenTrails":"0","color_sees_":"0","comeToFront":"0","comment_":"0","computeFunction_of_":"0","concatenate_with_":"0","contentsOfList_":"0","costumeIndex":"0","deleteLine_ofList_":"0","distanceTo_":"0","doAsk":"0","doBroadcastAndWait":"0","doForever":"3","doForeverIf":"0","doIf":"0","doIfElse":"0","doPlaySoundAndWait":"0","doRepeat":"0","doReturn":"0","doUntil":"0","doWaitUntil":"0","drum_duration_elapsed_from_":"0","filterReset":"0","forward_":"0","getAttribute_of_":"0","getLine_ofList_":"0","glideSecs_toX_y_elapsed_from_":"0","goBackByLayers_":"0","gotoSpriteOrMouse_":"0","gotoX_y_":"1","gotoX_y_duration_elapsed_from_":"0","heading":"0","heading_":"1","hide":"2","hideVariable_":"0","insert_at_ofList_":"0","isLoud":"0","keyPressed_":"0","letter_of_":"0","lineCountOfList_":"0","list_contains_":"0","lookLike_":"0","midiInstrument_":"0","motorOnFor_elapsed_from_":"0","mousePressed":"0","mouseX":"0","mouseY":"0","nextBackground":"0","nextCostume":"0","not":"0","noteOn_duration_elapsed_from_":"0","penColor_":"0","penSize_":"0","playSound_":"1","pointTowards_":"0","putPenDown":"0","putPenUp":"0","randomFrom_to_":"0","rest_elapsed_from_":"0","rewindSound_":"0","readVariable":"0","rounded":"0","say_":"3","say_duration_elapsed_from_":"0","sayNothing":"0","scale":"0","sensor_":"0","sensorPressed_":"0","setBlurTo_":"0","setBrightnessShiftTo_":"0","setFisheyeTo_":"0","setGraphicEffect_to_":"0","setHueShiftTo_":"0","setLine_ofList_to_":"0","setMosaicCountTo_":"0","setMotorDirection_":"0","setPenHueTo_":"0","setPenShadeTo_":"0","setPixelateCountTo_":"0","setPointillizeSizeTo_":"0","setSaturationShiftTo_":"0","setSizeTo_":"0","setStretchTo_":"0","setTempoTo_":"0","setVar_to_":"0","setVisibilityTo_":"0","setVolumeTo_":"0","setWaterRippleTo_":"0","setWhirlTo_":"0","show":"2","showBackground_":"0","showVariable_":"0","soundLevel":"0","sqrt":"0","stampCostume":"0","startMotorPower_":"0","stopAll":"0","stopAllSounds":"0","stringLength_":"0","tempo":"0","think_":"0","think_duration_elapsed_from_":"0","timer":"0","timerReset":"0","touching_":"0","touchingColor_":"0","turnAwayFromEdge":"0","turnLeft_":"0","turnRight_":"3","volume":"0","wait_elapsed_from_":"4","xpos":"0","xpos_":"0","yourself":"0","ypos":"0","ypos_":"0","askYahoo":"0","wordOfTheDay_":"0","jokeOfTheDay_":"0","synonym_":"0","info_fromZip_":"0","scratchrInfo_forUser_":"0","other":""}

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
	* This function returns  human-readable code of the latest version of the project (in json format)
	* Parameter: project id
	* Example:  http://scratch.mit.edu/api/getprojectblocks/1308192 
	* Output: {"project_id":"1308192","sprites":["","when I am clicked\n set \"arms\" to (pick random 4 to 20)\n set \"mini arm angle\" to (pick random 5 to 90)\n set \"mini arm length\" to (pick random 1 to 20)\n set \"mini arms\" to (pick random 1 to 4)\n set \"size\" to (pick random 10 to 150)\n set \"mainframe pen size\" to (pick random 1 to 5)\n set \"M.A. pen size\" to (pick random 1 to 5)\n\nwhen green flag clicked\n forever\n go to x: 0 y: -20\n if \n switch to costume \"costume2\"\n else\n switch to costume \"costume1\"\n\n","","when green flag clicked\n forever\n wait until \n wait until >\n go to x: 0 y: -20\n pen down\n clear\n point in direction 180\n set pen color to c[ebf7ff]\n repeat \"arms\"\n set pen size to \"mainframe pen size\"\n move (\"size\" \/ ((\"mini arms\" * 2) + 0.1)) steps\n repeat \"mini arms\"\n turn \"mini arm angle\" degrees\n set pen size to \"M.A. pen size\"\n move \"mini arm length\" steps\n move (0 - \"mini arm length\") steps\n turn (\"mini arm angle\" * 2) degrees\n move \"mini arm length\" steps\n move (0 - \"mini arm length\") steps\n turn \"mini arm angle\" degrees\n set pen size to \"mainframe pen size\"\n move (\"size\" \/ ((\"mini arms\" * 2) + 0.1)) steps\n go to x: 0 y: -20\n turn (360 \/ \"arms\") degrees\n\n","when green flag clicked\n go to x: 0 y: 0\n forever\n change y by -2\n wait 0 secs\n set x to 0\n if <(y position) < \"-318\">\n set y to 360\n\n","when green flag clicked\n go to x: 0 y: 360\n forever\n change y by -2\n wait 0 secs\n set x to 0\n if <(y position) < \"-318\">\n set y to 360\n\n"]}

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
	                	header('Content-Type: application/json');
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
	        header('Content-Type: application/json');
		echo "("; 
		echo json_encode($data);
		echo ")"; 
		exit;
	}//eof
	
	/**
	* This function returns latest project uploaded (in json format)
	* Parameter: authentication_key
	* Example: http://scratch.mit.edu/api/get_latest_project/XXXXXXXXXXXXX
	* Output: {"id":"13","thumbnailUrl":"http:\/\/scratch.mit.edu\/static\/projects\/demo\/13_med.png","uplodedIpAddress":"127.0.0.1"}
	*/
	function get_latest_project($auth_key = null){
		if(empty($auth_key) || trim($auth_key) !== GET_LATEST_PROJECT_AUTH_KEY){
			$errorMsg = array('error' =>'Invalid authentication key');
			header('Content-Type: application/json');
			echo json_encode($errorMsg);
			exit;
		}elseif( trim($auth_key) === GET_LATEST_PROJECT_AUTH_KEY)
		{
			$this->Project->unbindModel(
				array('hasMany' => array('GalleryProject'))
			);
			$project = $this->Project->find('first', array('order' => 'Project.created DESC', 'fields' => array('Project.id', 'Project.upload_ip', 'User.id', 'User.username')));
			$result = array(
							'id' 		   => $project['Project']['id'],
							'thumbnailUrl' => TOPLEVEL_URL. '/static/projects/' .$project['User']['username']. '/' .$project['Project']['id'] . '_med.png',
							'uplodedIpAddress'   => long2ip($project['Project']['upload_ip'])
							);
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}	
	}

	/**
	For a particular project, return the block count for each category.
	UNFINISHED
	**/
	function get_block_category_count($pid = null){

	// List of blocks per category

	  $blockGroupNames = array(
	      "Motion",
	      "Control",
	      "Looks",
	      "Sensing",
	      "Sounds",
	      "Operators",
	      "Pen",
	      "Variables"
	  );
	  $motionBlockSet = array(  //BLOCKS IN THE MOTION CATEGORY...
	      "forward_",
	      "turnLeft_",
	      "turnRight_",
	      "heading_",
	      "pointTowards_",
	      "gotoX_y_",
	      "gotoSpriteOrMouse_",
	      "glideSecs_toX_y_elapsed_from_",
	      "changeXposBy_",
	      "xpos_",
	      "changeYposBy_",
	      "ypos_",
	      "bounceOffEdge",
	      "xpos",
	      "ypos",
	      "heading"
	      //"gotoX_y_duration_elapsed_from_",
	      //"turnAwayFromEdge"
	  );

	  $controlBlockSet = array(  //...CONTROL GROUP... ETC
	      "EventHatMorph_StartClicked",
	      "KeyEventHatMorph",
	      "MouseClickEventHatMorph",
	      "wait_elapsed_from_",
	      "doForever",
	      "doRepeat",
	      "broadcast_",  
	      "doBroadcastAndWait",
	      //"WhenHatBlockMorph", //OBSOLETE
	      "EventHatMorph",  //When I receive
	      "doForeverIf",
	      "doIf",
	      "doIfElse",
	      "doReturn",
	      "doWaitUntil",
	      "doUntil",
	      "stopAll",
	  );

	  $looksBlockSet = array(  //LOOKS
	      "backgroundIndex",
	      //"changeBackgroundIndexBy_",
	      //"changeBlurBy_",
	      //"changeBrightnessShiftBy_",
	      //"changeCostumeIndexBy_",
	      //"changeFisheyeBy_",
	      "changeGraphicEffect_by_",
	      //"changeHueShiftBy_",
	      //"changeMosaicCountBy_",  
	      //"changePixelateCountBy_",
	      //"changePointillizeSizeBy_",
	      //"changeSaturationShiftBy_",
	      "changeSizeBy_",
	      //"changeStretchBy_",
	      //"changeVisibilityBy_",
	      //"changeWaterRippleBy_",
	      //"changeWhirlBy_",
	      "comeToFront",
	      "costumeIndex",
	      "filterReset",
	      "goBackByLayers_",
	      "hide",
	      "lookLike_",
	      "nextBackground",
	      "nextCostume",
	      "say_",
	      "say_duration_elapsed_from_",
	      //"sayNothing",
	      "scale",
	      //"setBlurTo_",
	      //"setBrightnessShiftTo_",
	      //"setFisheyeTo_",
	      "setGraphicEffect_to_",
	      //"setHueShiftTo_",
	      //"setMosaicCountTo_",
	      //"setPixelateCountTo_",
	      //"setPointillizeSizeTo_",
	      //"setSaturationShiftTo_",
	      "setSizeTo_",
	      //"setStretchTo_",
	      //"setVisibilityTo_",
	      //"setWaterRippleTo_",
	      //"setWhirlTo_",
	      "show",
	      "showBackground_",
	      "think_",
	      "think_duration_elapsed_from_",
	  );

	  $sensingBlockSet = array(  //SENSING
	      "answer",
	      "color_sees_",
	      "distanceTo_",
	      "doAsk",
	      "getAttribute_of_",
	      "isLoud",
	      "keyPressed_",
	      "mousePressed",
	      "mouseX",
	      "mouseY",
	      "sensor_",
	      "sensorPressed_",
	      "soundLevel",
	      "timer",
	      "timerReset",
	      "touching_",
	      "touchingColor_",
	  );

	  $soundBlockSet = array(  //SOUND
	      "changeVolumeBy_",
	      "doPlaySoundAndWait",
	      "drum_duration_elapsed_from_",
	      "midiInstrument_",
	      "noteOn_duration_elapsed_from_",
	      "playSound_",
	      //"rewindSound_", //obsolete
	      "setTempoTo_",
	      "changeTempoBy_",
	      "setVolumeTo_",
	      "stopAllSounds",
	      "tempo",
	      "volume",
	      "rest_elapsed_from_",
	  );

	  $operatorsBlockSet = array(  //OPERATORS
	      "and_operator",
	      "multiply_operator",
	      "add_operator",
	      "subtract_operator",
	      "divide_operator",
	      "isLessThan",
	      "isEqualTo",
	      "isGreaterThan",
	      "mod_operator",
	      "or_operator",
	      //"abs", //ADD TO computeFunction_of_
	      "concatenate_with_",
	      "letter_of_",
	      "not",
	      "randomFrom_to_",
	      "rounded",
	      //"sqrt", //ADD TO computeFunction_of_
	      "stringLength_",
	      "computeFunction_of_",
	  );

	  $penBlockSet = array(  //PEN
	      "changePenHueBy_",
	      "changePenShadeBy_",
	      "changePenSizeBy_", 
	      "clearPenTrails", 
	      "penColor_",
	      "penSize_",
	      "putPenDown",
	      "putPenUp",
	      "setPenHueTo_",
	      "setPenShadeTo_",
	      "stampCostume",
	  );

	  $variableBlockSet = array(  //VARIABLES AND LISTS
	      "append_toList_",
	      "changeVar_by_",
	      "contentsOfList_",
	      "deleteLine_ofList_",
	      "getLine_ofList_",
	      "hideVariable_",
	      "insert_at_ofList_",
	      "lineCountOfList_",
	      "list_contains_",
	      "readVariable",
	      "setLine_ofList_to_",
	      "setVar_to_",
	      "showVariable_",
	  );

	  $otherBlockSet = array(  //MOTOR AND WTF (feel free to categorize what you recognize) (no slice of pie for these guys)
	      "allMotorsOff",
	      "allMotorsOn",
	      "comment_",
	      "motorOnFor_elapsed_from_",
	      "startMotorPower_",
	      "setMotorDirection_",
	      "yourself",
	      "askYahoo",
	      "wordOfTheDay_",
	      "jokeOfTheDay_",
	      "synonym_",
	      "info_fromZip_",
	      "scratchrInfo_forUser_",
	      "other"
	  );
	}
}

?>	
