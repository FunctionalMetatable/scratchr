<?php
Class FeaturedGallery extends AppModel {
    var $name = "FeaturedGallery";
    var $belongsTo = array('Gallery' => array('className' => 'Gallery'));
	
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


    function bindGallery($conditions = null, $order = null) {
        $this->bindModel(array(
            'belongsTo' => array('Gallery' =>
             array('className' => 'Gallery',
                'conditions' => $conditions,
                'order' => $order
                ))));
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
}
?>
