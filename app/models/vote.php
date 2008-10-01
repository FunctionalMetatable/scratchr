<?php
Class Vote extends AppModel {
    var $name = 'Vote';
	
	/**
     * Private method to calculate project ratings
	 * @param int $prev_number_raters
	 * @param int $current_rating
	 * @param int $new_rating
     */
    function calcNewRating($prev_number_raters, $current_rating, $new_rating) {
        $total = $prev_number_raters * $current_rating;
        $total += $new_rating;
        return round($total/($prev_number_raters + 1));
    }
	
	function bindUser($conditions=null, $order=null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'User' =>
                array('className' => 'User',
					'conditions' => $conditions,
					'order' => $order,
					'foreignKey' => 'user_id'))));
    }
}
?>
