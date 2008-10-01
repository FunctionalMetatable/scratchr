<?php
Class SearchController extends AppController {

    /*------------------------------------*
    TODO: finish class
	
    A pagination class for pretty much
    most searchable model data that makes sense.
    *-------------------------------------*/

    var $uses = array('Thumbnail', 'Project', 'User', 'Notification');
    var $components = array('Pagination');
    var $helpers = array('Html', 'Pagination', 'Ajax');
    var $layout = 'default';

    function beforeFilter() {
        // only because we don't want to
        // have to do this in a modelname 
        // specific controller in accordance
        // with cake naming conventions
        // $this->modelClass = "Thumbnail";
        // parent::beforeFilter();
    }

    function thumbnails($pid=null) {
        $this->modelClass = "Thumbnail";
        $options = Array("sortBy"=>"created","showLimits"=>false);
        list($order,$limit,$page) = $this->Pagination->init($criteria=NULL, NULL, $options);
        $data = $this->Thumbnail->findAll($criteria=NULL, NULL, $order, $limit, $page);
    }

    /**
     * Query/GET params:
     * ?urlname=$urlname
     * ?page=$pagenumber
     */
    function projects($urlname=null) {
        $this->modelClass = "Project";
        $user = $this->User->find("User.urlname = '$urlname'");

        if (!empty($user)) {
            $user_id = $user['User']['id'];
            // return results for users' projects
            $options = Array("sortBy"=>"created","showLimits"=>false);
            $criteria = Array("user_id" => $user_id);
            list($order,$limit,$page) = $this->Pagination->init($criteria, NULL, $options);
            $this->{$this->modelClass}->bindThumbnail();
            $data = $this->Project->findAll($criteria, NULL, $order, $limit, $page);
            $this->set("urlname", $urlname);
            $this->set("data", $data);
            $this->render("XXXXXX");
        }
        exit();
    }

    /**
     * Query/GET params:
     * ?urlname=$urlname;
     */
    function users() {
        $this->modelClass = "User";
    }

    function binaries() {
        $this->modelClass = "Binarie";
    }

    /**
     * Qury/GET params
     * ?name=$tagname
     */
    function tags() {
        $this->modelClass = "Tag";
    }

    /**
     * Omni search
     * Query/GET params:
     * ?user=$urlname;
     * ?pid=$pid;
     */
    function index() {
    }
}
?>
