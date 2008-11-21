<?php
Class User extends AppModel
{
	var $name = 'User';	
	var $validate = array(
		'username' => '/^[a-zA-Z0-9-_]{3,20}$/',
		'password' => '/^.{6,40}$/',
		'email'	  => VALID_EMAIL,
		'gender'  => '/^male|female$/',
		'country' => VALID_NOT_EMPTY,
		'bmonth'  => '/^(1[0-2])|([1-9])$/',
		'byear'	  => '/^[0-9]{4}$/'
	);
	
	
  /**
   * Returns all user records
   * @return User array => array of user records
   */
  function getAllUsers() {
      return $this->findAll();
  }
  
  /**
   * Returns user with given project id
   * @param $pid => project id
   * @return User => User record object
   */
  function getUserByProjID($pid) {
  }

    /**
     * Returns user with given urlname
     * @param string urlName
     */
    function getUserByURL($urlname) {
    }

    /**
     * Returns user with given screenname
     * @param string screenname
     */
    function getUserByScreenName($screenname) {
    }

    /**
     * Returns user with given parameter name and selector constant
     * Selector constant is one of:
     * PROJECT_ID, SCREENNAME, USER_ID,
     * URLNAME, EMAIL
     */
    function getUser($parameter, $USER_SELECTOR) {
    }
	

    function bindProject($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Project' =>
                array('className' => 'Project',
                    'conditions' => $conditions))));
                    /*'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'user_id',
                    'dependent' => true,
                    'exclusive' => false))));
                    */
    }

    function unbindProject($conditions = null, $order = null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'Project' =>
                array('className' => 'Project'))));
    }
	
    function bindVote($conditions = null, $order = null, $limit=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Vote' =>
                array('className' => 'Vote',
                    'conditions' => $conditions,
                    'order' => $order,
					'limit' => $limit))));
    }
	
	function bindLover($conditions = null, $order = null, $limit=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Lover' =>
                array('className' => 'Lover',
                    'conditions' => $conditions,
                    'order' => $order,
					'limit' => $limit))));
    }
	
	function bindMyThemes($conditions=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Theme' => array(
                'className' => 'Theme',
                'joinTable' => 'theme_memberships',
                'foreignKey' => 'user_id',
                'associationForeignKey' => 'theme_id',
				'conditions' => $conditions,
				'uniq' => true))));	
	}
	
	function bindMyGalleries($conditions=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Gallery' => array(
                'className' => 'Gallery',
                'joinTable' => 'gallery_memberships',
                'foreignKey' => 'user_id',
                'associationForeignKey' => 'theme_id',
				'conditions' => $conditions,
				'uniq' => true))));	
	}
	
	function bindPermission($conditions=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Permission' => array(
                'className' => 'Permission',
                'joinTable' => 'permission_users',
                'foreignKey' => 'user_id',
                'associationForeignKey' => 'permission_id',
				'conditions' => $conditions,
				'uniq' => true))));	
	}
	
    function bindBookmarkedProjects($conditions=null, $order=null, $limit=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array('Project' =>
        array('className'  => 'Project',
            'joinTable'  => 'bookmarks',
            'foreignKey' => 'user_id',
            'associationForeignKey'=> 'project_id',
            'conditions' => $conditions,
			'limit' => $limit,
            'order' => $order))));
    }
	
	function tempblock($id) {
		$this->id=$id;
		$block_time = "+". TEMP_BLOCK_INTERVAL;
		$blocked_till=date("Y-m-d H:i:s", strtotime("$block_time", time()));
		$data['User']['blocked_till']=$blocked_till;
		$data['User']['status']='blockedtemporarily';
		$data['User']['id']=$id;
		return $this->save($data);
		}
	
	function tempunblock($id) {
		$this->id=$id;
		$data['User']['blocked_till']='0000-00-00 00:00';
		$data['User']['status']='normal';
		$data['User']['id']=$id;
		return $this->save($data);
	}
}
?>
