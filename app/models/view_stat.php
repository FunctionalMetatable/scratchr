<?php
Class ViewStat extends AppModel {
    var $name = 'ViewStat';
    var $belongsTo = array('Project' => array('className' => 'Project'));

    /*
    function bindProject() {
        $this->unbindModel(array(
        'belongsTo' => array(
            'Project' =>
                array('className' => 'Project'))));
    }*/

    function findIps($user_id) {
        $this->recursion = -1;
		$sql =  'SELECT INET_NTOA(ipaddress) ip, max(timestamp) AS v_time'
               . ' FROM view_stats'
               . ' WHERE user_id = '.$user_id
               . ' GROUP BY user_id, ipaddress ORDER BY v_time DESC';

        return $this->query($sql);
    }
}
?>
