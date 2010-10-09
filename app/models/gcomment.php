<?php
class Gcomment extends AppModel {
    var $name = 'Gcomment';
     var $belongsTo = array('User' => array('className' => 'User'), 'Gallery' => array ('className' => 'Gallery'));
	 
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

	function check($conditions = null, $safe, $admin = 0) {
		$temp_conditions = $conditions;
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

    function deleteCommentsFromMemcache($gallery_id) {
        $this->mc_connect();
        for($i=1; $i<=GCOMMENT_CACHE_NUMPAGE; $i++) {
            $mc_key = $gallery_id.'__'.$i;
            $this->mc_delete('gcomments', $mc_key);
            $mc_key = $gallery_id.'_1_'.$i;
            $this->mc_delete('gcomments', $mc_key);
        }
        //clear count cache
        $this->mc_delete('total_gcomments', $gallery_id);
        $this->mc_close();
    }
    
    function bindUser($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
                   /*'conditions' => $conditions,
                    'order' => $order,
                    'foreignKey' => 'user_id'
                     ))));*/
    }

   function unbindUser($conditions = null, $order = null) {
        $this->unbindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User'))));
   }
}
?>
