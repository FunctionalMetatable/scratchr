<?php
Class Project extends AppModel
{
  var $name = 'Project';

  var $validate = array(
    'name' => VALID_NOT_EMPTY,
    'description' => VALID_NOT_EMPTY
  );
  
  var $belongsTo = array('User' => array('className' => 'User'));
  var $hasMany = array('GalleryProject' => array('className' => 'GalleryProject'));

    /**
     * The following set of functions are overloaded cakePHP functions which
     * simply add the condition of proj_visibility='visible' to all sql queries.
     */
    function find($conditions=null, $fields=null, $order=null, $recursive=null, $safe="all")
    {
        return parent::find($conditions, $fields, $order, $recursive);
    }

    function read($fields=null, $id=null) 
    {
        return parent::read($fields, $id);
    }

    function findAll($conditions=null, $fields=null,$order=null,$limit=null,$page=1,$recursive=null, $safe="all", $admin = 0) 
    {
        return parent::findAll($this->check($conditions, $safe, $admin),$fields,$order,$limit,$page,$recursive);
    }

    function findCount($conditions=null, $recursive=0, $safe="all", $allowAll = false)
    {
       return parent::findCount($this->check($conditions, $safe, $allowAll), $recursive);
    }

    function addVisCheck($conditions = null, $admin = 0) 
    {	
		if ($admin == 1) {
			return $conditions;
		} else {
			$mycond = "(`proj_visibility`='visible' OR `proj_visibility`='censbycomm' OR `proj_visibility`='censbyadmin')" ;
			if (is_string($conditions) && strlen($conditions) > 0) 
				$mycond .= "AND $conditions";
			else if (is_array($conditions)) {
				foreach ($conditions as $key => $value) {
					if (is_string($value)) $mycond .= " AND `$key`='$value'";
					else $mycond .= " AND `$key`=$value";
				}
			}
			return $mycond;	
		}
	}

	function check($conditions = null, $safe, $admin = 0, $allowAll = false) {
        if($allowAll) {
            return $conditions;
        }
		$temp_conditions = $this->addVisCheck($conditions, $admin);
		$return_cond = $this->addSafeCheck($temp_conditions, $safe);
		return $return_cond;
	}
	
	function addSafeCheck($conditions = null, $content_level = "safe") {
		$isSafe=$this->getContentStatus();
		
		if ($content_level == "overload") {
			$content_level = "all";
		} else {
			if ($isSafe == "all") {
				$content_level = "all";
			} else {
				$content_level = "safe";
			}
		}
		if ($content_level == "all") {
			return $conditions;
		} else {
			$mycond = "`Project.status` ='safe'";
			if (is_string($conditions) && strlen($conditions) > 0) 
				$mycond .= "AND $conditions";
				else if (is_array($conditions)) {
				foreach ($conditions as $key => $value) {
					if (is_string($value)) $mycond .= " AND `$key`='$value'";
					else $mycond .= " AND `$key`=$value";
				}
			}
			return $mycond;
		}
	}
	
  /**
   * Dynamic model bindings/associations
   * e.g. To create 'Project hasMany Comments' that is only needed
   * sometimes and thus not created by defautl, create a wrapper:
   *
   * function bindComment($conditions = null, $order = null, $limit='5', $page='1') {
   *    $this->bindModel(array(
   *        'hasMany' => array(
   *            'Comment' =>
   *                array('className' => 'Comment',
   *                    'conditions' => $conditions,
   *                    'order' => $order,
   *                    'limit' => $limit,
   *                    'foreignKey' => 'post_id',
   *                    'depedent' => true,
   *                    'exclusive' => false,
   *                    'finderSql' => '',
   *                    'counterSql' => ''
   *                    ))));
   *                    }
   * Then in a ProjectsController method, call 
   * $this->Project->bindComment()
   * Note: bindModel() will also bind the association for one call to find().
   * In other words, the association is only active for one find()
   *
   * Technically you should be able to just specify the following in your
   * controller for cake to use default associations:
   * var $hasMany = array('Comment','Thumbnail');
   */
  
  /* 
   * Some variables that can be set
   * $transaction -> whether or not to enable transactions (i.e. begin/commit/rollback)
   * $useTable -> specify exact tablename to use
   * $validate -> array to validate data passed to this model
   *
   * associations: hasOne, hasMany, belongsTo, hasAndBelongsToMany
   */

    /* 
    define assoications
    When we execute find() or findAll() for Project model, we'll see the
    associated user
    perhaps too expensive
    var $belongsTo = array('User' => 
    array('className'=>'User',
          'conditions'=>'',
          'order'=>'',
          'foreignKey'=>'user_id'));
    */

    /**
     * Callback before every save() operation
     * TODO: access controls / validation
     */
    function beforeSave() {return true;}

    /**
     * Returns the project version
     * @param int $pid => project id
     * @return int => version number
     */
    function getVersion($pid=null) {
    }
    
    /**
     * Returns the description for the
     * given project. On another note, since
     * read() wraps around find(), it seems it
     * would be slower
     * @param int $pid => project id
     * @return string => text of the description
     */
    function getDescription($pid=null){
        return $this->read(PROJECT_DESCRIPTION, $pid);
    }

    /**
     * Returns the name of the project owner
     * @param int $pid => project id
     * @return string => name of owner
     */
    function getOwner($pid=null) {
        // return $this->read(PROJECT_USER_ID, $pid);
    }

    /**
     * Returns the project rating
     * @param int $pid => project id
     * @return int => integer rating value
     */
    function getRating($pid=null) {
        return $this->read(PROJECT_RATING, $pid);
    }
    
    /**
     * Returns the name of the project
     * @param int $pid => project id
     * @return string => name of project
     */
    function getName($pid=null) {
        return $this->read(PROJECT_NAME, $pid);
    }

    /**
     * Returns the number of unique views of the given project
     * @param int $pid => project id
     * @return int => number of views
     */
    function getViewCount($pid=null) {
        return $this->read(PROJECT_VIEWS, $pid);
    }
	
    /**
     * Returns an array of comments ordered by timestamp for the project
     * with the given project id.  The hash is of
     * the following form:
     * {array=>{$comment_id1, $text1, $user1, $time1},
     *  array=>{$comment_id2, $text2, $user2, $time2},
     *  ...
     * }
     * 
     * $comments = array();
     * while() {
     *  $comments[] = array('commentID'=>$id,
     *      'comentText'=>$text,
     *      'commentUser'=>$user,
     *      'commentTime'=>$timestamp);
     *      ...
     * }
     * @param int $pid => project id
     * @param int $limit => integer number of comments to return
     * @return array => array of comment descriptors
     */
    function getComments($pid=null, $limit=null) {
        $this->bindPcomment();
        $comments = array();
        // check if bindings also work with 'read()'
        // rather than just 'find()' and 'findAll()'
        $this->unbindPcomment();
        return $comments;
    }

    /**
     * Returns an array of tags associated with the
     * given project id. The hash is of the following
     * form:
     * {array=>{$projecttag_id1, $tag1}, 
     *  array=>{$projecttag_id2, $tag2},
     *  ...
     * }
     * @param int $pid => project id
     * @return array => aray of tag descriptors
     */
    function getTags($pid=null) {
    }

    /**
     * Returns a thumbnail record of the given project id and type
     * @param int $pid => project id
     */
    function getThumbnails($pid, $type='mini') {
        // bindModel dynamically: Projects => Thumbnails
    }

    /**
     * Modifies the description of the given project
     * @param int $pid => project id
     * @param string $description => new description text
     * @return true on success
     */
    function setDescription($new_desc, $pid=null) {
        return $this->saveField(PROJECT_DESCRIPTION, $new_desc);
    }

    /**
     * Modifies the title of the given project
     * @param int $pid => project id
     * @param string $title => new title string
     * @return true on success
     */
    function setName($title, $pid=null) {
        return $this->saveField(PROJECT_NAME, $title);
    }

    /**
     * Calculates the project rating
     * @param int $pid => project id
     * @return int => rating number for the project
     */
    function calcRating($pid=null) {
    }

    /**
     * Updates the rating for the project
     * @param int $pid => project id
     * @return true on success
     */
    function updateRating($pid=null){
    }

    /**
     * Calculates the number of views for the project
     * @param int $pid => project id
     * @return int => view count for the project
     */
    function calcViews($pid=null) {
    }

    /**
     * Updates the view count for the project
     * @param int $pid => project id
     * @return true on success
     */
    function updateViewCount($pid=null) {
    }

    /**
     * Change thumbnail associated with the project
     * @param enum $type => THUMBNAIL_MINI || THUMBNAIL_MEDIUM
     * @param int $pid => project id
     * @param file $file => file handle for new thumbnail
     * @return true on success
     */
    function changeThumbnail($type, $file, $pid=null) {
    }

    /**
     * Change binary associated with the project
     * @param string $mimetype => mimetype for the binary
     * @param file $file => file handle for new binary
     * @param int $pid => project id
     * @return true on success
     */
    function changeBinary($mimetype, $file, $pid=null) {
    }

    /**
     * Sets project permissions
     * @param array $permissions => array(PERMISSION_VIEW => true, ...);
     * @param int $pid => project id
     */
    function setPermissions($permission, $pid=null) {
    }

    /**
     * Sets project tags
     * @param array $tags => array of string tags
     * @param int $pid => project id
     * @return true on success
     */
    function setTags($tag, $pid=null) {
    }

    /**
     * Adds tags the the project
     * @param array $tags => array of string tags
     * @param int $pid => project id
     * @return true on success
     */
    function addTags($tag, $pid=null) {
    }

    /**
     * Adds comments to the project
     * @param array $comments => array of comments (see getComments())
     * @param int $pid => project id
     * @return true on success
     */
    function addComments($comments, $pid=null) {
    }


    /**
     * Used by remove, censor, uncensor. Writes visibility change info
     * to database.
     */
    function beforeVisChange($pid, $urlname, $user_id, $hide) {
        $this->id = $pid;
        $project = $this->read();
		
        $this->saveField('vischangedbyid', $user_id);
        $this->saveField('vischangedtime', date("YmdHis"));
		
        $sm_thumb = WWW_ROOT . getThumbnailImg($urlname, $pid, 'mini', false, DS);
        $med_thumb = WWW_ROOT . getThumbnailImg($urlname, $pid, 'med', false, DS);
        $bin_file = WWW_ROOT . getBinary($urlname, $pid, false, DS);

		foreach (array($sm_thumb, $med_thumb, $bin_file) as $file) {
                if ($hide == true) {
                    if (file_exists($file)) {
						$old_file = $file;
						$new_file = $old_file . ".hid";
                        rename($file, $new_file); 
                    }
                } else if ($hide == false) {
					$old_file = $file . ".hid";
					$new_file = str_replace(".hid", "", $old_file);
                    if (file_exists($old_file)) {
                        chmod($old_file, 0644);
                        rename($old_file, $new_file);
                    }
                }
        }
    }



    /**
     * Private per project delete helper
     * @param int $pid => project id
     * @param string $urlname => owner urlname
     * @param bool $isadmin => is user who requested action an admin?
     * @param int $user_id => id of user initiating action
     */
    function remove($pid, $urlname, $isadmin, $user_id) {
        $this->beforeVisChange($pid, $urlname, $user_id, true);
        if ($isadmin == true)
            $this->saveField('proj_visibility', 'delbyadmin');
        else
            $this->saveField('proj_visibility', 'delbyusr');
			
        /* 
         * below is analogous to $this->delete($pid, true)
         * except wihtout deleting itself
         *
         * $this->bindPcomment();
         * $this->bindHABTMTag();
         * $this->bindView();
         * $this->bindLover();
         * $this->bindFlagger();
         */
        $this->bindFavorite();
        $this->bindFeatured();
        $this->bindHABTMTheme();
        $this->id = $pid;	
	
        if ($this->exists() && $this->beforeDelete()) {
            $db =& ConnectionManager::getDataSource($this->useDbConfig);
            $this->_deleteLinks($pid);
            $this->_deleteDependent($pid, true);
            $this->_deleteDependent($pid, true);
        }
    } 
	
	
    /**
     * Private per project censor helper
     * @param int $pid => project id
     * @param string $urlname => owner urlname
     * @param bool $isadmin => is user who initiated action an admin?
     * @param int $user_id => id of user requesting action
     */
    function censor($pid, $urlname, $isadmin, $user_id) {
        $this->beforeVisChange($pid, $urlname, $user_id, true);
        if ($isadmin == true)
            $this->saveField('proj_visibility', 'censbyadmin');
        else
            $this->saveField('proj_visibility', 'censbycomm');
    } 

	
    /**
     * Private per project uncensor helper
     * @param int $pid => project id
     * @param string $urlname => owner urlname
     * @param bool $isadmin => is user initating action admin?
     * @param int $user_id => id of user initiating action
     */
    function uncensor($pid, $urlname, $isadmin, $user_id) {
        $this->beforeVisChange($pid, $urlname, $user_id, false);
        $this->saveField('flagit', 0);
        $this->saveField('proj_visibility', 'visible');
		$this->saveField("status", "notreviewed");
    } 

	
	
    // FOR DYNAMIC MODEL ASSOCIATIONS: note the unbinds() are not necessary
    // in this case...i just realized that. Only use them if you were setting
    // global/static assocations with something like "var $hasMany..." and 
    // you want to dynamically dissociate. These dynamic associations/dissocations
    // are only valid for one call
    

    function bindPcomment($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Pcomment' =>
                array('className' => 'Pcomment',
					'dependent' => true, 
                    'conditions' => $conditions))));
    }

   function unbindPcomment($conditions = null, $order = null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'Pcomment' =>
                array('className' => 'Pcomment',
					'dependent' => true))));
   }

	function bindFeatured() {
        $this->bindModel(array(
        'hasOne' => array(
            'FeaturedProject' =>
                array('className' => 'FeaturedProject',
					'dependent' => true))));	
	}


	function bindHABTMTheme($conditions=null, $order=null, $limit=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Gallery' => array(
                'className' => 'Gallery',
                'joinTable' => 'gallery_projects',
                'foreignKey' => 'project_id',
                'associationForeignKey' => 'gallery_id',
				'uniq' => true,
	            'conditions' => $conditions,
				'limit' => $limit,
				'order' => $order))));	
	}
	
   /**
    * Join table HABTM association
    */
   function bindHABTMTag() {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array(
            'Tag' => array(
                'className' => 'Tag',
                'joinTable' => 'project_tags',
                'foreignKey' => 'project_id',
                'associationForeignKey' => 'tag_id',
				'uniq' => true))));
   }

   function bindProjectTag($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'ProjectTag' =>
                array('className' => 'ProjectTag',
					'dependent' => true))));
                    /*'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'project_id',
                    'dependent' => true,
                    'exclusive' => false
                    ))));*/
    }

    function unbindProjectTag($conditions = null, $order = null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'ProjectTag' =>
                array('className' => 'ProjectTag',
                    'conditions' => $conditions,
                    'order' => $order))));
    }
	
	function bindGalleryProject($conditions = null, $order = null) {
	$this->bindModel(array(
        'hasMany' => array(
            'GalleryProject' =>
                array('className' => 'GalleryProject',
                    'conditions' => $conditions,
                    'order' => $order))));
	}
	
    function bindThumbnail($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Thumbnail' =>
                array('className' => 'Thumbnail',
                    'conditions' => $conditions))));
                    /*'order' => $order,
                    'foreignKey' => 'project_id',
                    'dependent' => true,
                    'exclusive' => false))));*/
    }

    function unbindThumbnail($conditions=null, $order=null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'Thumbnail' =>
                array('className' => 'Thumbnail',
                    'conditions' => $conditions,
                    'order' => $order))));
    }

    function bindBinary($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasOne' => array(
            'Binarie' =>
                array('className' => 'Binarie'))));
                   /*'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'project_id',
                    'dependent' => true))));*/
    }

    function unbindBinary($conditions=null, $order=null) {
        $this->unbindModel(array(
        'hasOne' => array(
            'Binarie' =>
                array('className' => 'Binarie'))));
    }

    function bindUser($conditions=null, $order=null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
    }

    function bindVote($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Vote' =>
                array('className' => 'Vote',
					'dependent' => true))));
                    /*'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'project_id',
                    'dependent' => true,
                    'exclusive' => false))));*/
    }

    function unbindVote($conditions=null, $order=null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'Vote' =>
                array('className' => 'Vote',
                    'conditions' => $conditions,
                    'order' => $order))));
    }

    function bindPermission($condition=null, $order=null) {
        $this->bindModel(array(
        'hasOne' => array(
            'Permission' =>
                array('className' => 'Permission',
                    'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'project_id',
                    'dependent' => true))));
    }

    function unbindPermission($condition=null, $order=null) {
        $this->unbindModel(array(
        'hasOne' => array(
            'Permission' =>
                array('className' => 'Permission'))));
    }

    function bindView($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'View' =>
                array('className' => 'ViewStat',
					'dependent' => true))));
    }

    function unbindView($conditions=null, $order=null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'View' =>
                array('className' => 'ViewStat'))));
    }

    function bindFavorite($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Favorite' =>
                array('className' => 'Favorite',
					'dependent' => true))));
    }

    function bindLover($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Lover' =>
                array('className' => 'Lover',
					'dependent' => true))));
    }
	
	    function bindFlagger($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Flagger' =>
                array('className' => 'Flagger',
					'dependent' => true))));
    }
	
    function unbindFavorite($condition=null, $order=null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'Favorite' =>
                array('className' => 'Favorite'))));
    }
	
    function bindBookmark($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Bookmark' =>
                array('className' => 'Bookmark',
					'dependent' => true))));
    }

    function unbindBookmark($condition=null, $order=null) {
        $this->unbindModel(array(
        'hasMany' => array(
            'Bookmark' =>
                array('className' => 'Bookmark'))));
    }
	
	 function bindProjectFlag($conditions=null, $order=null) {
        $this->bindModel(array(
        'hasMany' => array(
            'ProjectFlag' =>
                array('className' => 'ProjectFlag',
					'dependent' => true))));
    }
	
	function set_loveits($id, $love_its) {
		if ($id) {
			$this->id = $id;
			$this->saveField('loveit', $love_its);
		}
	}
	
	function set_loveitsuniqueip($id, $love_its) {
		if ($id) {
			$this->id = $id;
			$this->saveField('loveitsuniqueip', $love_its);
		}
	}

    function register_frontpage($project_ids, $type) {
        if(!empty($project_ids)) {
            //the following code will create some part of the SQL efficiently, sacrificing the readability
            $values = "(" . implode($project_ids, ", '$type'), (") . ", '$type')";
            $sql = "INSERT IGNORE INTO `projects_frontpage` ( `project_id` ,  `type` ) VALUES ". $values;
            $this->query($sql);
        }
    }

    //clear the based on cache for this project
    function clear_based_on_data($project_id) {
        $this->mc_connect();
        $this->mc_delete('based_on_data', $project_id);
        $this->mc_close();
    }

    //populate friends 3 latest project
    function getMyFriendsLatest3Projects($user_id) {
        $my_friends_latest3_projects = $this->mc_get('my_friends_latest3_projects', $user_id);
        if (!$my_friends_latest3_projects) {
       	    $project_ids = array();
            list($project_count, $project_ids) = $this->__getMyFriendsProjectIds($user_id);
            $my_friends_latest3_projects = $this->__getProjectsFromIds($project_ids, 0, 3);
            $this->mc_set('my_friends_latest3_projects',
                $my_friends_latest3_projects, $user_id, HOME_FRIENDS_PROJECTS_TTL);
        }
        return $my_friends_latest3_projects;
    }

    function getMyFriendsLatestProjectsCount($user_id) {
        $this->mc_connect();
        $project_count = 0;
        list($project_count, $project_ids) = $this->__getMyFriendsProjectIds($user_id);
        $this->mc_close();
        return $project_count;
    }
    
    function getMyFriendsLatestProjects($user_id, $page, $limit) {
        $this->mc_connect();
        $my_friends_latest_projects = $this->mc_get('my_friends_latest_projects',
            $user_id.'_'.$page.'_'.$limit);

        if (!$my_friends_latest_projects) {
            $project_ids = array();
            list($project_count, $project_ids) = $this->__getMyFriendsProjectIds($user_id);
            $offset = ($page-1)*$limit;
            $my_friends_latest_projects = $this->__getProjectsFromIds($project_ids, $offset, $limit);
            $this->mc_set('my_friends_latest_projects',
                $my_friends_latest_projects, $user_id.'_'.$page.'_'.$limit, HOME_FRIENDS_PROJECTS_TTL);
        }
        $this->mc_close();
        return $my_friends_latest_projects;
    }
    
	function __getProjectsFromIds($project_ids, $offset = 0, $limit = 10) {
        if(empty($project_ids)) {
            return array();
        }

        $projects =array();
        $project_ids = array_slice($project_ids, $offset, $limit);
        $project_ids = implode(',', $project_ids);
        $this->unbindModel(
            array('hasMany' => array('GalleryProject'))
        );
        $projects = $this->findAll('Project.id in ('.$project_ids.')',
                           null, 'Project.created DESC');
        
        return $projects;
	}

    function __getMyFriendsProjectIds($user_id) {
        $project_data = $this->mc_get('friends_projects_data', $user_id);

        if(!$project_data) {
            $project_count = 0;
            $project_ids = array();
            
            $config = $this->User->getdbName();
            $mysqli = new mysqli($config['host'], $config['login'],
                                $config['password'], $config['database']);
            $rs = $mysqli->query( "CALL latest1friendproject($user_id)" );
            
            while($project_count <= MAX_FRIENDS_PROJECTS && $row = $rs->fetch_object()) {
                array_push($project_ids, $row->id);
                $project_count++;
            }
            mysqli_free_result($rs);
            mysqli_close($mysqli);
            $project_data = array($project_count, $project_ids);
            $this->mc_set('friends_projects_data', $project_data, $user_id, HOME_FRIENDS_PROJECTS_TTL);
        }
        
        return $project_data;
    }

    function getTopProjects($fields, $order_by_clause, $interval, $exclude_project_ids,
                    $exclude_user_ids, $limit, $condition = null, $join_table = null) {

        $exclude_clause = '';
		$exclude_user_id_clause = '';
        $condition_clause = '';
        $interval_clause = '';
        
        if(!empty($exclude_project_ids)) {
            $exclude_clause = ' AND `projects`.`id` NOT IN ( '
                                    . implode($exclude_project_ids, ' , ').' )';
        }
		if(!empty($exclude_user_ids)) {
            $exclude_user_id_clause = ' AND `projects`.`user_id` NOT IN ( '
                                     . implode($exclude_user_ids, ' , ') . ' )';
        }
        if(!empty($condition)) {
            $condition_clause = ' AND ' . $condition;
        }
        if(!empty($interval)) {
            $interval_clause = ' AND `created` > now( ) - INTERVAL '. $interval .' DAY';
        }
        if(!empty($join_table)) {
            $join_table = ', ' . $join_table;
        }
        if(!empty($fields)) {
            $fields = ', ' . $fields;
        }

        $order_by_clause_inner = ' ORDER BY ' . $order_by_clause;
        $order_by_clause_outer = '';
        if($order_by_clause != 'RAND()') {
            $order_by_clause_outer = ' ORDER BY `Project`.'.$order_by_clause;
        }

        $sql = 'SELECT `Project`.id, `Project`.name, `User`.urlname'
               .' FROM ('
               .' SELECT `projects`.`id`, `projects`.`user_id`, `name`'.$fields
               .' FROM `projects`' . $join_table
               .' WHERE `projects`.`user_id` > 0 '
               . $interval_clause
               . $condition_clause
               .' AND `proj_visibility` = "visible" AND `status` <> "notsafe"'
               . $exclude_clause . $exclude_user_id_clause
               . $order_by_clause_inner
               .' LIMIT ' . ($limit * 5)
               .' ) `Project`'
               .' LEFT JOIN `users` `User` ON `Project`.`user_id` = `User`.`id`'
               .' GROUP BY `Project`.`user_id`'
               . $order_by_clause_outer
               .' LIMIT ' . $limit;

        return $this->query($sql);
    }
}
?>