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
			$this->Project->mc_set("get-project-creators", $tot_creators, false, API_PROJECT_CREATORS_TTL);
		}
		echo $tot_creators;
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
			$this->Project->mc_set("get-total-projects", $tot_projects, false, API_TOTAL_PROJECT_TTL);
		}
		echo $tot_projects;
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
			$this->Project->mc_set("get-total-scripts", $tot_scripts, false, API_TOTAL_SCRIPTS_TTL);
		}
		echo $tot_scripts;
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
			$this->Project->mc_set("get-total-sprites", $tot_sprites, false, API_TOTAL_SPRITES_TTL);
		}
		echo $tot_sprites;
		exit;
	}
}
?>
