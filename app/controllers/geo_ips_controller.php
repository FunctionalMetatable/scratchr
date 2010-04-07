<?php
class GeoIpsController extends AppController {
    var $uses = array('Project');
    var $components = array();
    var $helpers = array();

        function set_country_code($page = 0) {
                $this->Project->recursive = -1;
                $projects = $this->Project->findAll("Project.upload_ip IS NOT NULL AND (Project.country IS NULL OR Project.country ='')",
                                                'id, inet_ntoa(upload_ip) as ip, country', 'id DESC', 100, $page. -1, 'all', 1);
                echo 'processing page '.$page;
                foreach($projects as $project){
                        $upload_ip = $project['0']['ip'];
                        $countryName = $this->GeoIp->lookupCountryCode($upload_ip);
                        $this->Project->id = $project['Project']['id'];
                        $this->Project->saveField('country', $countryName);
                        $this->Project->id = false;
                }

                echo 'done';exit;
        }//function
}

?>