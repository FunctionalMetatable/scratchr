<?php
class DisposableDomain extends AppModel {
    var $name = 'DisposableDomain';
	
	function isBlacklisted($email) {
		$domain = strtolower(substr($email, strrpos($email, '@') + 1));
        $blacklist = $this->find('list', array('fields' => 'tld'));
        if(in_array($domain, $blacklist)) {
            return true;
    	}
       	
    	return false;
	}
}
?>
