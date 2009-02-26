<?php
Class Gallery extends AppModel {

    var $name = 'Gallery';
    var $belongsTo = array('User' => array('className' => 'User'));

	function remove($theme_id) {
		// delete theme icon
		// delete theme_memberships
		// delete theme table
		// delete theme_projects
		// delete tcomments
		$this->bindTcomment();
		$this->bindHABTMProject();
		$this->bindHABTMMembers();		
		$this->bindFeatured();
		$this->delete($theme_id, true);
        $icon_file = WWW_ROOT . getThemeIcon($theme_id, false, DS);
		if (file_exists($icon_file))
			unlink($icon_file);	
		return true;
	}
	
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

    function findCount($conditions=null, $recursive=0, $safe="all")
    {
       return parent::findCount($this->check($conditions, $safe), $recursive);
    }

	function bindGcomment($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Gcomment' =>
                array('className' => 'Gcomment',
					'dependent' => true, 
			'order' => 'timestamp DESC',
                    'conditions' => $conditions))));
    }
	
    function bindHABTMProject($conditions=null, $order=null, $limit=null, $page = 1) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array('Project' =>
			array('className'  => 'Project',
				'joinTable'  => 'gallery_projects',
				'foreignKey' => 'gallery_id',
				'associationForeignKey'=> 'project_id',
				'conditions' => $conditions,
				'limit' => $limit,
				'order' => $order,
				'page' => $page))));
    }

    function bindHABTMMembers($conditions=null, $order=null, $limit=null) {
        $this->bindModel(array(
        'hasAndBelongsToMany' => array('User' =>
			array('className'  => 'User',
				'joinTable'  => 'gallery_memberships',
				'foreignKey' => 'gallery_id',
				'associationForeignKey'=> 'user_id',
				'conditions' => $conditions,
				'limit' => $limit,
				'order' => $order))));
    }
	
	
	function bindFeatured() {
        $this->bindModel(array(
        'hasOne' => array(
            'FeaturedGallery' =>
                array('className' => 'FeaturedGallery',
					'dependent' => true))));	
	}
	
	 function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
	 }
	 
	 function check($conditions = null, $safe, $admin = 0) {
		$temp_conditions = $conditions;
		$return_cond = $this->addSafeCheck($temp_conditions, $safe);
		return $return_cond;
	}
	
	function addSafeCheck($conditions = null, $content_level = "safe") {
		if ($content_level == "overload") {
			$content_level = "all";
		} else {
			if (CONTENT_STATUS == "all") {
				$content_level = "all";
			} else {
				$content_level = "safe";
			}
		}
		
		if ($content_level == "all") {
			return $conditions;
		} else {
			$mycond = "`Gallery.status` ='safe'";
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
	
	function set_status($id, $status) {
		if ($id) {
			$this->id = $id;
			$this->saveField('status', $status);
		}
	}
	
	function remove_project($id) {
		if ($id) {
			$this->id = $id;
			$old_count = $this->field('total_projects',"id = $id");
			$new_count = $old_count - 1;
			$this->saveField('total_projects', $new_count);
		}
	}
	
	function add_project($id) {
		if ($id) {
			$this->id = $id;
			$old_count = $this->field('total_projects',"id = $id");
			$new_count = $old_count + 1;
			$this->saveField('total_projects', $new_count);
		}
	}

    function register_frontpage($gallery_ids, $type) {
        if(!empty($gallery_ids)) {
            //the following code will create some part of the SQL efficiently, sacrificing the readability
            $values = "(" . implode($gallery_ids, ", '$type'), (") . ", '$type')";
            $sql = "INSERT IGNORE INTO `galleries_frontpage` ( `gallery_id` ,  `type` ) VALUES ". $values;
            $this->query($sql);
        }
    }
}
?>
