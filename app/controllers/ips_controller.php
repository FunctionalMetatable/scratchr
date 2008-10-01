<?php
class IpsController extends AppController {
    var $uses = array();
    var $components = array(); 
    var $helpers = array(); 

	function view() {
		$client_ip = $this->RequestHandler->getClientIP();
		echo "getClientIP: '$client_ip'";
		$long = ip2long($client_ip);
		echo "<br>ip2long('$client_ip')='$long'";
		exit;

	}
}

?>
