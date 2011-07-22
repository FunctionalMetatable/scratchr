<?php
class Integraflag extends AppModel
{
    var $name = 'Integraflag';
    
    function findStatsByAction($start, $end) {
      $start = date('Y-m-d', $start);
      $end = date('Y-m-d', $end);
      $sql = "SELECT (DATE(`created`)) AS date, 
                      `action`, 
                      COUNT(*) AS count 
              FROM `integraflags`
              WHERE `created` >= '$start' AND `created` <= '$end' 
              GROUP BY DATE(`created`), `action`
              ORDER BY `created`";
      return $this->query($sql);
    }
    
    function findStatsByType($start, $end) {
      $start = date('Y-m-d', $start);
      $end = date('Y-m-d', $end);
      $sql = "SELECT (DATE(`created`)) AS date,
                      `type`,
                      COUNT(*) AS count
              FROM `integraflags`
              WHERE `created` >= '$start' AND `created` <= '$end'
              GROUP BY DATE(`created`), `action`
              ORDER BY `created`";
      return $this->query($sql);
    }
}


?>
