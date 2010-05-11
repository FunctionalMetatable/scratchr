<?php
class UtilHelper extends Helper {
    function username($username, $role, $possesive = false) {
        $asterisk = "";
        if($role=='admin') {
            $asterisk = "<a  class='asterisk' href=" .INFO_URL. '/Admins' ." >*</a>";

        }
        $extra = $possesive ? ___("'s", true) : '';
        return "<a href=\"/users/{$username}\">{$username}</a>{$extra}{$asterisk} ";
    }
    
   /**
	* Helper for setting up auto html links for urls in comments
	**/
    function linkify($content) {
        $pattern = '/\b(https?:\/\/)?(www.)?[A-Z0-9.]*('
                    . WHITELISTED_URL_PATTERN
                    . ')[-A-Z0-9+&@#()\/%?=~_|!:,.;]*/i';

        return preg_replace_callback(
                $pattern, array( &$this,'linkify_cb'),
                $content
            );
    }

    /**
     * Callback function for linkify's preg_replace
     **/
    function linkify_cb($matches) {
        $url = $text = $matches[0];
        $url_texts = array(
            TOPLEVEL_URL.'/projects' => ___('link to project', true),
            TOPLEVEL_URL.'/galleries' => ___('link to gallery', true),
            TOPLEVEL_URL.'/forums' => ___('link to forums', true),
        );
        foreach($url_texts as $u => $t) {
            if(strpos($url, $u) !== false) {
                $text = '('.$t.')';
                break;
            }
        }
        if(strpos($url, "http://") !== 0) { $url = "http://" . $url; }

        return "<a href=\"{$url}\">{$text}</a>";
    }
	
	/*
	Check if gallery description contains a whitelist url 
	*/
	 function check_url($url){
		if (preg_match('/(https?:\/\/)?(www.)?([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) {
   		$pattern = '/\b(https?:\/\/)?(www.)?[A-Z0-9.]*('
                    . WHITELISTED_URL_PATTERN
                    . ')[-A-Z0-9+&@#()\/%?=~_|!:,.;]*/i';
			if (preg_match ($pattern, $url)) 
			{
				return true;
			}else{
			return false;
			}
		} else {
	   return true;
		}
	}//function
}
?>
