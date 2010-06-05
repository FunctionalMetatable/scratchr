<?php
class GeoIpComponent extends Object {
    function lookupIp($ip) {
        vendor("geoipcity");

        $gi = geoip_open("files/geolitecity.dat", GEOIP_STANDARD);
        $result = geoip_record_by_addr($gi, $ip);
        geoip_close($gi);
        
        return get_object_vars($result);
    }
	
	function lookupCountryCode($ip){
		$path  =  APP.'misc/';
		App::import('Vendor', 'Example', array('file' => 'GeoIp'.DS.'geoip.inc'));
		// read GeoIP database
		$handle = geoip_open($path."GeoIP.dat", GEOIP_STANDARD);
		return geoip_country_code_by_addr($handle, $ip);
	}
    
    function findIp() {
      if(getenv("HTTP_CLIENT_IP"))
        return getenv("HTTP_CLIENT_IP"); 
      elseif(getenv("HTTP_X_FORWARDED_FOR"))
        return getenv("HTTP_X_FORWARDED_FOR"); 
      else 
        return getenv("REMOTE_ADDR"); 
    }
}

?> 
