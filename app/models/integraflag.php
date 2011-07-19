<?php
class Integraflag extends AppModel
{
    var $name = 'Integraflag';
    
    function findStatsByAction($start=null, $end=null) {
      if(!$start) $start = date('Y-m-d', time()-(60*60*24*90));
      if(!$end) $end = date('Y-m-d', time()+1000);
      $sql = "SELECT UNIX_TIMESTAMP(DATE(`created`)) AS date, 
                      `action`, 
                      COUNT(*) AS count 
              FROM `integraflags`
              WHERE `created` > '$start' AND `created` <= '$end' 
              GROUP BY DATE(`created`), `action`
              ORDER BY `created`";
      return $this->query($sql);
    }
    
    function findStatsByType($start=null, $end=null) {
      if(!$start) $start = date('Y-m-d', time()-(60*60*24*90));
      if(!$end) $end = date('Y-m-d', time()+1000);
      $sql = "SELECT UNIX_TIMESTAMP(DATE(`created`)) AS date,
                      `type`,
                      COUNT(*) AS count
              FROM `integraflags`
              WHERE `created` > '$start' AND `created` <= '$end'
              GROUP BY DATE(`created`), `action`
              ORDER BY `created`";
      return $this->query($sql);
    }
}


?>
