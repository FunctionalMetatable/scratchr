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
		echo "<br>HTTP_X_FORWARDED_FOR:" . env('HTTP_X_FORWARDED_FOR');
		echo "<br>HTTP_CLIENT_IP:" .  env('HTTP_CLIENT_IP');
		echo "<br>REMOTE_ADDR:" . env('REMOTE_ADDR');
		echo "<br>HTTP_CLIENTADDRESS:" . env('HTTP_CLIENTADDRESS');
		exit();
	}
}

?>
