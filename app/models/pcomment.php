<?php
class Pcomment extends AppModel {
    var $name = 'Pcomment';
    var $belongsTo = array('User' => array('className' => 'User'), 'Project' => array ('className' => 'Project'));
    
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
    
    function bindProject($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project'))));
    }
	
	function bindMpcomment($conditions = null, $order = null) {
        $this->bindModel(array(
        'hasMany' => array(
            'Mpcomment' =>
                array('className' => 'Mpcomment',
					  'foreignKey' => 'comment_id'
				))));
    }
	
	function set_temporary_block($id) {
		$this->id=$id;
		$pcomment = $this->read();
		$comment_created_date_time = $pcomment['Pcomment']['created'];
		$block_time = "-" . BLOCK_CHECK_INTERVAL;
		$block_time = date("Y-m-d H:i:s", strtotime("$block_time", time()));
		if($comment_created_date_time >= $block_time) {	
			$this->bindMpcomment();
			$flags=$this->Mpcomment->findCount("Mpcomment.comment_id =".$id);	
			if($flags >= NUM_MAX_COMMENT_FLAGS) {
				$this->bindUser();
				$this->User->tempblock($pcomment['Pcomment']['user_id']);
			}
		}
	}
}
?>