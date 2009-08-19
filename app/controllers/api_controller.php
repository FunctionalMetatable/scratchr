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

}
?>
