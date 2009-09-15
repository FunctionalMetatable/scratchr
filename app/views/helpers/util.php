<?php
class UtilHelper extends Helper
	{
		function username($username, $role, $possesive = false) {
    		$asterisk = "";
    		if($role=='admin') {
        		$asterisk = "<a  class='asterisk' href=" .INFO_URL. '/Admins' ." >*</a>";
				
    		}
            $extra = $possesive ? ___("'s", true) : '';
            return "<a href=\"/users/{$username}\">{$username}</a>{$extra}{$asterisk} ";
		}
		
	}
 
?>
